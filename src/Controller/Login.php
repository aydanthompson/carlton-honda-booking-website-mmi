<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Service\UserAuthenticationService;
use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Login
{
  private $request;
  private $response;
  private $renderer;
  private $userAuthenticationService;

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer,
    UserAuthenticationService $userAuthenticationService
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
    $this->userAuthenticationService = $userAuthenticationService;
  }

  public function show(): Response
  {
    if ($this->request->isMethod('POST')) {
      $email = $this->request->get('email');
      $password = $this->request->get('password');

      $user = $this->userAuthenticationService->authenticate($email, $password);
      if ($user) {
        $_SESSION['user'] = serialize($user);
        return new JsonResponse(['success' => true]);
      } else {
        // Message is displayed above the login form.
        return new JsonResponse(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
      }
    }

    $html = $this->renderer->render('Homepage', []);
    $this->response->setContent($html);
    return $this->response;
  }
}
