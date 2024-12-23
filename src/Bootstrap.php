<?php

declare(strict_types=1);

namespace CarltonHonda;

use Dotenv\Dotenv;
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

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

// Load environment variables.
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Error handler.
$whoops = new Run;
if ($_ENV['ENVIRONMENT'] !== 'production') {
  $whoops->pushHandler(new PrettyPageHandler);
} else {
  $whoops->pushHandler(function ($e) {
    echo 'TODO: User friendly error page and email functionality.';
  });
}
$whoops->register();

$injector = require __DIR__ . '/Dependencies.php';

$request = $injector->make(Request::class);
$response = $injector->make(Response::class);

session_start();

// Load routes.
$routesConfig = require __DIR__ . '/Routes.php';
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
