<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use CarltonHonda\Model\User;
use CarltonHonda\Service\BookingService;
use CarltonHonda\Template\FrontendRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ManageBookingsController
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

  public function show(): Response
  {
    if (!isset($_SESSION['user'])) {
      return new RedirectResponse('/');
    }

    $user = unserialize($_SESSION['user']);
    if ($user instanceof User) {
      if ($user->getRoleName() === 'employee' || $user->getRoleName() === 'admin') {
        $bookings = $this->bookingService->getAllBookings();
      } else {
        $bookings = $this->bookingService->getBookingsByUserId($user->getId());
      }

      $html = $this->renderer->render('ManageBookings', [
        'bookings' => $bookings,
        'user' => $user
      ]);
      $this->response->setContent($html);
      return $this->response;
    } else {
      return new RedirectResponse('/');
    }
  }

  public function cancelBooking(): Response
  {
    $bookingId = $this->request->get('bookingId');
    if ($this->bookingService->cancelBooking((int)$bookingId)) {
      return new RedirectResponse('/profile/manage-bookings');
    } else {
      // FIXME: This alert looks rubbish.
      $this->response->setContent('<script>alert("Booking cancellation failed. Please contact Carlton Honda."); window.location.href="/profile/manage-bookings";</script>');
      $this->response->setStatusCode(500);
      return $this->response;
    }
  }
}
