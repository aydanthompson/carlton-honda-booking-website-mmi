<?php

declare(strict_types=1);

use CarltonHonda\Controller\Homepage;

return [
  'routes' => [
    'hello_world' => [
      'path' => '/',
      '_controller' => [Homepage::class, 'show']
    ],
    'another_route' => [
      'path' => '/another-route',
      '_controller' => function () {
        echo 'This works too!';
      }
    ]
  ]
];
