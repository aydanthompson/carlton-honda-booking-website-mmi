<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Contact
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
    if ($this->request->isMethod('POST')) {
      $name = $this->request->get('name');
      $email = $this->request->get('email');
      $message = $this->request->get('message');

      if ($this->sendEmail($name, $email, $message)) {
        $data['success'] = 'Your message has been sent successfully.';
      } else {
        $data['error'] = 'An error occurred. Please try again.';
      }
    }

    $html = $this->renderer->render('Contact', $data ?? []);
    $this->response->setContent($html);
  }

  private function sendEmail($name, $email, $message)
  {
    echo "hello";
    $mail = new PHPMailer(true);

    try {
      // Server settings.
      $mail->isSMTP();
      $mail->Host = $_ENV['SMTP_HOST'];
      $mail->SMTPAuth = true;
      $mail->Username = $_ENV['SMTP_USER'];
      $mail->Password = $_ENV['SMTP_PASS'];
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      // Recipients.
      $mail->setFrom($_ENV['SMTP_USER'], 'Carlton Honda Support');
      $mail->addAddress($_ENV['SMTP_USER'], 'Carlton Honda Support');
      $mail->addReplyTo($email, $name);

      // Content.
      $mail->isHTML(true);
      $mail->Subject = 'Support Request';
      $mail->Body = nl2br($message);

      $mail->send();
      return true;
    } catch (Exception $e) {
      throw $e;
      return false;
    }
  }
}
