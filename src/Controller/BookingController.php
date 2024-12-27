<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Service\BookingService;
use CarltonHonda\Service\EmailService;
use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController
{
  private $request;
  private $response;
  private $renderer;
  private $bookingService;
  private $emailService;

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer,
    BookingService $bookingService,
    EmailService $emailService
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
    $this->bookingService = $bookingService;
    $this->emailService = $emailService;
  }

  public function show()
  {
    if (isset($_SESSION['email'])) {
      $data['services'] = ['MOT', 'Service', 'Repair'];
    }

    $html = $this->renderer->render('Booking', $data ?? []);
    $this->response->setContent($html);
  }

  public function getAvailableSlots(): JsonResponse
  {
    $date = $this->request->query->get('date');
    $availableSlots = $this->bookingService->getAvailableSlots($date);
    return new JsonResponse($availableSlots);
  }

  public function getDaysWithFreeSlots(): JsonResponse
  {
    $daysWithFreeSlots = $this->bookingService->getDaysWithFreeSlots();
    return new JsonResponse($daysWithFreeSlots);
  }

  public function bookSlot(): JsonResponse
  {
    $data = json_decode($this->request->getContent(), true);
    $date = $data['date'];
    $time = $data['time'];

    $email = $_SESSION['email'];
    $userId = $this->bookingService->getUserIdByEmail($email);
    if ($userId === null) {
      return new JsonResponse(['success' => false, 'message' => 'User not found']);
    }

    $slotId = $this->bookingService->getSlotId($date, $time);
    if ($slotId && $this->bookingService->bookSlot($slotId, $userId)) {
      // Send confirmation email
      $this->sendConfirmationEmail($email, $date, $time);
      return new JsonResponse(['success' => true]);
    }

    return new JsonResponse(['success' => false]);
  }

  private function sendConfirmationEmail(string $email, string $date, string $time): void
  {
    $subject = 'Booking Confirmation';
    $body = "Your booking for $date at $time has been confirmed.";
    $this->emailService->sendEmail($email, $subject, $body);
  }
}
