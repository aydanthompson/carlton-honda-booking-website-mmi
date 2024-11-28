<?php

declare(strict_types=1);

namespace CarltonHonda;

require __DIR__ . "/../vendor/autoload.php";

error_reporting(E_ALL);

$environment = "development";

$whoops = new \Whoops\Run;
if ($environment !== "production") {
  $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
  $whoops->pushHandler(function ($e) {
    echo "TOD0: User friendly error page and email functionality.";
  });
}
$whoops->register();

throw new \Exception;
