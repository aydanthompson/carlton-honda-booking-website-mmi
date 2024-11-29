<?php

declare(strict_types=1);

use Auryn\Injector;
use CarltonHonda\Template\MustacheRenderer;
use CarltonHonda\Template\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$injector = new Injector();

$injector->alias(Request::class, Request::class);
$injector->share(Request::class);
$injector->define(Request::class, [
  ':query' => $_GET,
  ':request' => $_POST,
  ':cookies' => $_COOKIE,
  ':files' => $_FILES,
  ':server' => $_SERVER,
]);

$injector->alias(Response::class, Response::class);
$injector->share(Response::class);

$injector->alias(Renderer::class, MustacheRenderer::class);
$injector->define('Mustache_Engine', [
  ':options' => [
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__DIR__) . '/templates', [
      'extension' => '.html',
    ]),
  ],
]);

return $injector;
