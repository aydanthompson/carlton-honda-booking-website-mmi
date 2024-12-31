<?php

declare(strict_types=1);

use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/../.env')) {
  // Development path.
  $_ENV['PUBLIC_DIR'] = realpath(__DIR__);
  $_ENV['PRIVATE_DIR'] = realpath(__DIR__ . '/..');
} elseif (file_exists(__DIR__ . '/../private/.env')) {
  // Production path.
  $_ENV['PUBLIC_DIR'] = realpath(__DIR__);
  $_ENV['PRIVATE_DIR'] = realpath(__DIR__ . '/../private');
} else {
  throw new Exception('No environment file found.');
}

require $_ENV['PRIVATE_DIR'] . '/vendor/autoload.php';

// Load environment variables.
$dotenv = Dotenv::createImmutable($_ENV['PRIVATE_DIR']);
$dotenv->load();

require $_ENV['PRIVATE_DIR'] . "/src/Bootstrap.php";
