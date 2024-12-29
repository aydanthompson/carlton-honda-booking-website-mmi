<?php

declare(strict_types=1);

namespace CarltonHonda\Model;

class User
{
  private $id;
  private $email;
  private $roleName;

  public function __construct(int $id, string $email, string $roleName)
  {
    $this->id = $id;
    $this->email = $email;
    $this->roleName = $roleName;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getRoleName(): string
  {
    return $this->roleName;
  }
}
