<?php

declare(strict_types=1);

namespace CarltonHonda\Template;

interface Renderer
{
  public function render($template, $data = []): string;
}
