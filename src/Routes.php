<?php

declare(strict_types=1);

use CarltonHonda\Controller\BookingController;
use CarltonHonda\Controller\Contact;
use CarltonHonda\Controller\Homepage;
use CarltonHonda\Controller\Login;
use CarltonHonda\Controller\Logout;
use CarltonHonda\Controller\Page;
use CarltonHonda\Controller\Register;

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
    'get_days_with_free_slots' => [
      'path' => '/online-booking/get-days-with-free-slots',
      '_controller' => [BookingController::class, 'getDaysWithFreeSlots']
    ],
    'online_booking_book_slot' => [
      'path' => '/online-booking/book-slot',
      '_controller' => [BookingController::class, 'bookSlot']
    ],
    'slug' => [
      'path' => '/{slug}',
      '_controller' => [Page::class, 'show']
    ]
  ]
];
