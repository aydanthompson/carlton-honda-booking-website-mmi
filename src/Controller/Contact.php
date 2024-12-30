<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Service\EmailService;
use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Contact
{
  private $request;
  private $response;
  private $renderer;
  private $emailService;

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer,
    EmailService $emailService
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
    $this->emailService = $emailService;
  }

  public function show()
  {
    if ($this->request->isMethod('POST')) {
      $name = $this->request->get('name');
      $email = $this->request->get('email');
      $message = $this->request->get('message');

      if ($this->emailService->sendEmail($_ENV['SMTP_USER'], 'Support Request', $message, null, $email)) {
        $data['success'] = 'Your message has been sent successfully.';
      } else {
        $data['error'] = 'An error occurred. Please try again.';
      }
    }

    $html = $this->renderer->render('Contact', $data ?? []);
    $this->response->setContent($html);
  }
}
