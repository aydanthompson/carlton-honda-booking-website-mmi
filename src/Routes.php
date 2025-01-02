<?php

declare(strict_types=1);

use CarltonHonda\Controller\BookingController;
use CarltonHonda\Controller\Contact;
use CarltonHonda\Controller\Homepage;
use CarltonHonda\Controller\Login;
use CarltonHonda\Controller\Logout;
use CarltonHonda\Controller\ManageBookingsController;
use CarltonHonda\Controller\Page;
use CarltonHonda\Controller\Register;
use CarltonHonda\Controller\VehicleDetailsController;

return [
  'routes' => [
    'hello_world' => [
      'path' => '/',
      '_controller' => [Homepage::class, 'show']
    ],
    'register' => [
      'path' => '/register',
      '_controller' => [Register::class, 'show']
    ],
    'login' => [
      'path' => '/login',
      '_controller' => [Login::class, 'show']
    ],
    'logout' => [
      'path' => '/logout',
      '_controller' => [Logout::class, 'show']
    ],
    'contact' => [
      'path' => '/contact',
      '_controller' => [Contact::class, 'show']
    ],
    'online_booking' => [
      'path' => '/online-booking',
      '_controller' => [BookingController::class, 'show']
    ],
    'online_booking_get_available_slots' => [
      'path' => '/online-booking/get-available-slots',
      '_controller' => [BookingController::class, 'getAvailableSlots']
    ],
    'online_booking_get_days_with_free_slots' => [
      'path' => '/online-booking/get-days-with-free-slots',
      '_controller' => [BookingController::class, 'getDaysWithFreeSlots']
    ],
    'online_booking_book_slot' => [
      'path' => '/online-booking/book-slot',
      '_controller' => [BookingController::class, 'bookSlot']
    ],
    'profile_manage_bookings' => [
      'path' => '/profile/manage-bookings',
      '_controller' => [ManageBookingsController::class, 'show']
    ],
    'profile_manage_bookings_cancel' => [
      'path' => '/profile/manage-bookings/cancel',
      '_controller' => [ManageBookingsController::class, 'cancelBooking']
    ],
    'vehicle_details' => [
      'path' => '/vehicle-details',
      '_controller' => [VehicleDetailsController::class, 'dvlaVesCheck']
    ],
    'slug' => [
      'path' => '/{slug}',
      '_controller' => [Page::class, 'show']
    ]
  ]
];
