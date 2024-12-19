<?php

declare(strict_types=1);

use Auryn\Injector;
use CarltonHonda\Menu\ArrayMenuReader;
use CarltonHonda\Menu\MenuReader;
use CarltonHonda\Page\FilePageReader;
use CarltonHonda\Page\PageReader;
use CarltonHonda\Service\UserAuthentication;
use CarltonHonda\Service\UserRegistration;
use CarltonHonda\Template\FrontendRenderer;
use CarltonHonda\Template\FrontendTwigRenderer;
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
  $twig->addGlobal('session', $_SESSION);
  return $twig;
});

$injector->define(FilePageReader::class, [
  ':pageFolder' => __DIR__ . '/../pages',
]);

$injector->alias(PageReader::class, FilePageReader::class);
$injector->share(FilePageReader::class);

$injector->alias(FrontendRenderer::class, FrontendTwigRenderer::class);

$injector->alias(MenuReader::class, ArrayMenuReader::class);
$injector->share(ArrayMenuReader::class);

$injector->share(PDO::class);
$injector->define(PDO::class, [
  ':dsn' => 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],
  ':username' => $_ENV['DB_USER'],
  ':passwd' => $_ENV['DB_PASS'],
]);

// Services.
$injector->share(UserRegistration::class);
$injector->share(UserAuthentication::class);

return $injector;
