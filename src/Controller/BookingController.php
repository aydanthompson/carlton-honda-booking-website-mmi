<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Service\BookingService;
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

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer,
    BookingService $bookingService
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
    $this->bookingService = $bookingService;
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
    $response = new JsonResponse($availableSlots);
    return $response;
  }

  public function getDaysWithFreeSlots(): JsonResponse
  {
    $daysWithFreeSlots = $this->bookingService->getDaysWithFreeSlots();
    return new JsonResponse($daysWithFreeSlots);
  }
