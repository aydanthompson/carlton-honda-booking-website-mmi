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

  public function bookSlot(int $userId, string $date, string $time): bool
  {
    $this->pdo->beginTransaction();
    try {
      $stmt = $this->pdo->prepare('INSERT INTO bookings (user_id, service, date, time) VALUES (:userId, :service, :date, :time)');
      $stmt->execute([
        'userId' => $userId,
        'service' => 'MOT',
        'date' => $date,
        'time' => $time
      ]);

      $stmt = $this->pdo->prepare('UPDATE available_slots SET is_booked = 1 WHERE date = :date AND time = :time');
      $stmt->execute([
        'date' => $date,
        'time' => $time
      ]);

      $this->pdo->commit();
      return true;
    } catch (\Exception $e) {
      error_log($e->getMessage());
      $this->pdo->rollBack();
      return false;
    }
  }

  public function getBookingsByUserId(int $userId): array
  {
    $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE user_id = :userId');
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllBookings(): array
  {
    $stmt = $this->pdo->query('SELECT * FROM bookings');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function cancelBooking(int $bookingId): bool
  {
    $this->pdo->beginTransaction();
    try {
      $stmt = $this->pdo->prepare('SELECT date, time FROM bookings WHERE id = :bookingId');
      $stmt->execute(['bookingId' => $bookingId]);
      $booking = $stmt->fetch();

      if (!$booking) {
        throw new \Exception('Booking not found');
      }

      $stmt = $this->pdo->prepare('DELETE FROM bookings WHERE id = :bookingId');
      $stmt->execute(['bookingId' => $bookingId]);

      $stmt = $this->pdo->prepare('UPDATE available_slots SET is_booked = 0 WHERE date = :date AND time = :time');
      $stmt->execute([
        'date' => $booking['date'],
        'time' => $booking['time']
      ]);

      $this->pdo->commit();
      return true;
    } catch (\Exception $e) {
      error_log($e->getMessage());
      $this->pdo->rollBack();
      return false;
    }
  }
}
