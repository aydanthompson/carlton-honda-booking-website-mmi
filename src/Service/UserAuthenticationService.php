<?php

declare(strict_types=1);

namespace CarltonHonda\Service;

use PDO;
use CarltonHonda\Model\User;

class UserAuthenticationService
{
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function authenticate(string $email, string $password): ?User
  {
    $stmt = $this->pdo->prepare('
      SELECT users.*, user_roles.role_name 
      FROM users 
      JOIN user_roles ON users.role_id = user_roles.id 
      WHERE email = :email
    ');
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password_hash'])) {
      return new User(
        (int)$row['id'],
        $row['email'],
        $row['role_name']
      );
    }
    return null;
  }
}
