<?php

declare(strict_types=1);

return [
  'routes' => [
    'hello_world' => [
      'path' => '/',
      '_controller' => [CarltonHonda\Controller\Homepage::class, 'show']
    ],
    'another_route' => [
      'path' => '/another-route',
      '_controller' => function () {
        echo 'This works too!';
      }
    ]
  ]
];
