<?php

declare(strict_types=1);

namespace CarltonHonda\Menu;

class ArrayMenuReader implements MenuReader
{
  public function readMenu(): array
  {
    return [
      ['href' => '/', 'text' => 'Home'],
      ['href' => '/book', 'text' => 'Online Booking'],
      ['href' => '/contact', 'text' => 'Contact Us'],
    ];
  }
}
