<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CarltonHonda\Service\UserRegistration;

class Register
{
  private $request;
  private $response;
  private $renderer;
  private $registrationService;

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer,
    UserRegistration $registrationService
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
    $this->registrationService = $registrationService;
  }

  public function show()
  {
    if ($this->request->isMethod('POST')) {
      $email = $this->request->get('email');
      $password = $this->request->get('password');

      if ($this->registrationService->register($email, $password)) {
        $this->response->headers->set('Location', '/');
        $this->response->setStatusCode(302);
        return;
      } else {
        $data['error'] = 'An error occured. Please try again.';
      }
    }

    $html = $this->renderer->render('Register', $data ?? []);
    $this->response->setContent($html);
  }
}
