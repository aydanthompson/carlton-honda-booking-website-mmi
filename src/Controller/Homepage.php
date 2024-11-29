<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Homepage
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
    $content = '<h1>Hello, world!</h1>';
    $content .= '<p>Hello ' . $this->request->get('name', 'stranger') . '!<p>';
    $this->response->setContent($content);
    return $this->response;
  }
}
