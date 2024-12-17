<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Logout
{
  private $request;
  private $response;

  public function __construct(Request $request, Response $response)
  {
    $this->request = $request;
    $this->response = $response;
  }

  public function show()
  {
    session_destroy();
    $this->response->headers->set('Location', '/');
    $this->response->setStatusCode(302);
  }
}
