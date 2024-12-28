<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CarltonHonda\Service\UserAuthenticationService;

class Login
{
  private $request;
  private $response;
  private $renderer;
  private $authenticationService;

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer,
    UserAuthenticationService $authenticationService
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
    $this->authenticationService = $authenticationService;
  }

  public function show()
  {
    if ($this->request->isMethod('POST')) {
      $email = $this->request->get('email');
      $password = $this->request->get('password');

      if ($this->authenticationService->authenticate($email, $password)) {
        $_SESSION['email'] = $email;
        $this->response->headers->set('Location', '/');
        $this->response->setStatusCode(302);
        return;
      } else {
        $_SESSION['flash'] = 'Invalid credentials. Please try again.';
        $this->response->headers->set('Location', '/');
        $this->response->setStatusCode(302);
        return;
      }
    }

    $html = $this->renderer->render('Homepage', []);
    $this->response->setContent($html);
  }
}
