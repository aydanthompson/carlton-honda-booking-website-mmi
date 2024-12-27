<?php

declare(strict_types=1);

namespace CarltonHonda\Service;

use Exception;
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

  public function getUserIdByEmail(string $email): ?int
  {
    $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetchColumn();
    return $result !== false ? (int) $result : null;
  }

  public function getSlotId(string $date, string $time): ?int
  {
    $stmt = $this->pdo->prepare('SELECT id FROM available_slots WHERE date = :date AND time = :time AND is_booked = 0');
    $stmt->execute(['date' => $date, 'time' => $time]);
    $result = $stmt->fetchColumn();
    return $result !== false ? (int) $result : null;
  }

  public function bookSlot(int $slotId, int $userId): bool
  {
    $this->pdo->beginTransaction();

    try {
      $stmt = $this->pdo->prepare('UPDATE available_slots SET is_booked = 1 WHERE id = :slotId AND is_booked = 0');
      $stmt->execute(['slotId' => $slotId]);

      if ($stmt->rowCount() === 1) {
        $stmt = $this->pdo->prepare('INSERT INTO bookings (user_id, slot_id) VALUES (:userId, :slotId)');
        $stmt->execute(['userId' => $userId, 'slotId' => $slotId]);
        $this->pdo->commit();
        return true;
      }

      $this->pdo->rollBack();
      return false;
    } catch (Exception $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }
}
