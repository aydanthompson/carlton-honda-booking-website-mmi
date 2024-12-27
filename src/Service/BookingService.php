<?php

declare(strict_types=1);

namespace CarltonHonda\Service;

use PDO;

class BookingService
{
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function getAvailableSlots(string $date): array
  {
    $stmt = $this->pdo->prepare('SELECT time FROM available_slots WHERE date = :date AND is_booked = 0');
    $stmt->execute(['date' => $date]);
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the slots to include the time key
    return array_map(function ($slot) {
      return ['time' => $slot['time']];
    }, $slots);
  }

  public function getDaysWithFreeSlots(): array
  {
    $stmt = $this->pdo->query('SELECT DISTINCT date FROM available_slots WHERE is_booked = 0');
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
