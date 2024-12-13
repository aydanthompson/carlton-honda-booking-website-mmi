<?php

declare(strict_types=1);

use CarltonHonda\Controller\Homepage;
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
    'slug' => [
      'path' => '/{slug}',
      '_controller' => [Page::class, 'show']
    ]
  ]
];
