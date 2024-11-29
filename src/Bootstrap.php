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

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

$environment = 'development';

// Error handler.
$whoops = new Run;
if ($environment !== 'production') {
  $whoops->pushHandler(new PrettyPageHandler);
} else {
  $whoops->pushHandler(function ($e) {
    echo 'TODO: User friendly error page and email functionality.';
  });
}
$whoops->register();

$injector = require 'Dependencies.php';

$request = $injector->make(Request::class);
$response = $injector->make(Response::class);

// Load routes from Routes.php
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

  if (is_array($controller)) {
    // Controller is a class name and method.
    $className = $controller[0];
    $class = $injector->make($className);
    $method = $controller[1];
    $controllerInstance = [$class, $method];
  } else {
    // Controller is a callable.
    $controllerInstance = $controller;
  }

  call_user_func($controllerInstance, $attributes);
} catch (ResourceNotFoundException $e) {
  $response->setContent('404 - Page not found');
  $response->setStatusCode(404);
} catch (MethodNotAllowedException $e) {
  $response->setContent('405 - Method not allowed');
  $response->setStatusCode(405);
}

$response->send();
