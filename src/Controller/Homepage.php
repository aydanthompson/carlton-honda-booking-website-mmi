<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Homepage
{
  private $request;
  private $response;
  private $renderer;

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
  }

  public function show()
  {
    $data = ['name' => $this->request->get('name', 'stranger')];
    $html = $this->renderer->render('Homepage', $data);
    $this->response->setContent($html);
  }
}
