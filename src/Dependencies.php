<?php

declare(strict_types=1);

use Auryn\Injector;
use CarltonHonda\Page\FilePageReader;
use CarltonHonda\Page\PageReader;
use CarltonHonda\Template\MustacheRenderer;
use CarltonHonda\Template\Renderer;
use CarltonHonda\Template\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

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

$injector->alias(Renderer::class, TwigRenderer::class);
$injector->define(Mustache_Engine::class, [
  ':options' => [
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__DIR__) . '/templates', [
      'extension' => '.html',
    ]),
  ],
]);
$injector->delegate(Environment::class, function () use ($injector) {
  $loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
  $twig = new Environment($loader);
  return $twig;
});

$injector->define(FilePageReader::class, [
  ':pageFolder' => __DIR__ . '/../pages',
]);

$injector->alias(PageReader::class, FilePageReader::class);
$injector->share(FilePageReader::class);

return $injector;
