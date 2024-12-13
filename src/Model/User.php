<?php

declare(strict_types=1);

namespace CarltonHonda\Model;

class User
{
  private $id;
  private $username;
  private $passwordHash;
  private $email;
  private $createdAt;
  private $updatedAt;

  public function __construct(
    int $id,
    string $username,
    string $passwordHash,
    string $email,
    string $createdAt,
    string $updatedAt
  ) {
    $this->id = $id;
    $this->username = $username;
    $this->passwordHash = $passwordHash;
    $this->email = $email;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  // Add getters for the properties
}
