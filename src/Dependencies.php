<?php

declare(strict_types=1);

use Auryn\Injector;
use CarltonHonda\Menu\ArrayMenuReader;
use CarltonHonda\Menu\MenuReader;
use CarltonHonda\Page\FilePageReader;
use CarltonHonda\Page\PageReader;
use CarltonHonda\Service\UserRegistration;
use CarltonHonda\Template\FrontendRenderer;
use CarltonHonda\Template\FrontendTwigRenderer;
use CarltonHonda\Template\MustacheRenderer;
use CarltonHonda\Template\Renderer;
use CarltonHonda\Template\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

include __DIR__ . '../../database_config.php';

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

$injector->alias(FrontendRenderer::class, FrontendTwigRenderer::class);

$injector->alias(MenuReader::class, ArrayMenuReader::class);
$injector->share(ArrayMenuReader::class);

$injector->share(PDO::class);
$injector->define(PDO::class, [
  ':dsn' => "mysql:host=$db_host;dbname=$db_name",
  ':username' => $db_user,
  ':passwd' => $db_pass,
]);

$injector->share(UserRegistration::class);

return $injector;
