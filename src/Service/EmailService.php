<?php

declare(strict_types=1);

namespace CarltonHonda\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
  private $mailer;

  public function __construct(PHPMailer $mailer)
  {
    $this->mailer = $mailer;
  }

  public function sendEmail(string $to, string $subject, string $body, string $from = null, string $replyTo = null): bool
  {
    try {
      // Server settings
      $this->mailer->isSMTP();
      $this->mailer->Host = $_ENV['SMTP_HOST'];
      $this->mailer->SMTPAuth = true;
      $this->mailer->Username = $_ENV['SMTP_USER'];
      $this->mailer->Password = $_ENV['SMTP_PASS'];
      $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $this->mailer->Port = 587;

      // Recipients
      $this->mailer->setFrom($from ?? $_ENV['SMTP_USER'], 'Carlton Honda Support');
      $this->mailer->addAddress($to);
      if ($replyTo) {
        $this->mailer->addReplyTo($replyTo);
      }

      // Content
      $this->mailer->isHTML(true);
      $this->mailer->Subject = $subject;
      $this->mailer->Body = nl2br($body);

      $this->mailer->send();
      return true;
    } catch (Exception $e) {
      error_log('Email sending failed: ' . $e->getMessage());
      return false;
    }
  }
}
