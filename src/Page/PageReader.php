<?php

declare(strict_types=1);

namespace CarltonHonda\Page;

interface PageReader
{
  public function readBySlug(string $slug): string;
}
