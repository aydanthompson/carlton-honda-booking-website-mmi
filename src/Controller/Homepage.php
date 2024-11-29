<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use Symfony\Component\HttpFoundation\Response;

class Homepage
{
  private $response;

  public function __construct(Response $response)
  {
    $this->response = $response;
  }

  public function show()
  {
    $this->response->setContent('Hello, world!');
  }
}
