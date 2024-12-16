<?php

declare(strict_types=1);

use CarltonHonda\Controller\Homepage;
use CarltonHonda\Controller\Login;
use CarltonHonda\Controller\Logout;
use CarltonHonda\Controller\Page;
use CarltonHonda\Controller\Register;

return [
  'routes' => [
    'hello_world' => [
      'path' => '/',
      '_controller' => [Homepage::class, 'show']
    ],
    'register' => [
      'path' => '/register',
      '_controller' => [Register::class, 'show']
    ],
    'login' => [
      'path' => '/login',
      '_controller' => [Login::class, 'show']
    ],
    'logout' => [
      'path' => '/logout',
      '_controller' => [Logout::class, 'show']
    ],
    'slug' => [
      'path' => '/{slug}',
      '_controller' => [Page::class, 'show']
    ]
  ]
];
