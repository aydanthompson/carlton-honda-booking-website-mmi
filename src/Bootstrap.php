<?php

declare(strict_types=1);

namespace CarltonHonda;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

error_reporting(E_ALL);

// Error handler.
$whoops = new Run;
if ($_ENV['ENVIRONMENT'] !== 'production') {
  $whoops->pushHandler(new PrettyPageHandler);
} else {
  $whoops->pushHandler(function ($e) {
    // Very basic alert, redirects to homepage.
    $script = '<script type="text/javascript">';
    $script .= 'alert("An unexpected error has occured. Redirecting you to the homepage.");';
    $script .= 'window.location.href = "/";';
    $script .= '</script>';
    echo $script;
  });
}
$whoops->register();

$injector = require $_ENV['PRIVATE_DIR'] . '/src/Dependencies.php';

$request = $injector->make(Request::class);
$response = $injector->make(Response::class);

session_start();

// Load routes.
$routesConfig = require $_ENV['PRIVATE_DIR'] . '/src/Routes.php';
$routes = new RouteCollection();
foreach ($routesConfig['routes'] as $name => $route) {
  $routes->add($name, new Route($route['path'], ['_controller' => $route['_controller']]));
}

// Match the request to a route.
$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
  $attributes = $matcher->match($request->getPathInfo());
  $controller = $attributes['_controller'];
  unset($attributes['_controller']);

  $className = $controller[0];
  $class = $injector->make($className);
  $method = $controller[1];
  $controllerInstance = [$class, $method];

  $result = call_user_func($controllerInstance, $attributes);
  // Controller methods that return response objects override the default response.
  if ($result instanceof Response) {
    $response = $result;
  }
} catch (ResourceNotFoundException $e) {
  $response->setContent('404 - Page not found');
  $response->setStatusCode(404);
} catch (MethodNotAllowedException $e) {
  $response->setContent('405 - Method not allowed');
  $response->setStatusCode(405);
}

$response->send();
