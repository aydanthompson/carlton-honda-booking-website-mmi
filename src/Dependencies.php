<?php

declare(strict_types=1);

use Auryn\Injector;
use CarltonHonda\Menu\ArrayMenuReader;
use CarltonHonda\Menu\MenuReader;
use CarltonHonda\Page\FilePageReader;
use CarltonHonda\Page\PageReader;
use CarltonHonda\Service\BookingService;
use CarltonHonda\Service\UserAuthenticationService;
use CarltonHonda\Service\UserRegistrationService;
use CarltonHonda\Template\FrontendRenderer;
use CarltonHonda\Template\FrontendTwigRenderer;
use CarltonHonda\Template\MustacheRenderer;
use CarltonHonda\Template\Renderer;
use CarltonHonda\Template\TwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

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
  $loader = new FilesystemLoader(dirname(__DIR__) . '/templates');
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
$injector->delegate(PDO::class, function () {
  $host = $_ENV['DB_HOST'];
  $db = $_ENV['DB_NAME'];
  $user = $_ENV['DB_USER'];
  $pass = $_ENV['DB_PASS'];
  $charset = 'utf8mb4';
  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  return new PDO($dsn, $user, $pass, $options);
});

// Services.
$injector->share(UserRegistrationService::class);
$injector->share(UserAuthenticationService::class);
$injector->share(BookingService::class);

return $injector;
