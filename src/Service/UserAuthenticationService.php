<?php

declare(strict_types=1);

namespace CarltonHonda\Service;

use PDO;

class UserAuthenticationService
{
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function authenticate(string $email, string $password): bool
  {
    $stmt = $this->pdo->prepare('SELECT password_hash FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $passwordHash = $stmt->fetchColumn();

    if ($passwordHash && password_verify($password, $passwordHash)) {
      return true;
    }

    return false;
  }
}
