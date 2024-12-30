<?php

declare(strict_types=1);

namespace CarltonHonda\Service;

use PDO;

class UserRegistrationService
{
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function register(string $email, string $password): bool
  {
    $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
      return false;
    }

    $stmt = $this->pdo->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)');
    return $stmt->execute([
      'email' => $email,
      'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);
  }
}
