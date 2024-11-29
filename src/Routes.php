<?php

declare(strict_types=1);

return [
  'routes' => [
    'hello_world' => [
      'path' => '/hello-world',
      'controller' => function () {
        echo 'Hello, world!';
      }
    ],
    'another_route' => [
      'path' => '/another-route',
      'controller' => function () {
        echo 'This works too!';
      }
    ]
  ]
];
