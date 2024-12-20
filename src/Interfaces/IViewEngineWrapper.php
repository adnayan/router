<?php

declare(strict_types=1);

namespace Nayan\Router\Interfaces;

interface IViewEngineWrapper
{
    public function render(string $view, array $content): string;
}