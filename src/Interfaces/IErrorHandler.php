<?php

declare(strict_types=1);

namespace Nayan\Router\Interfaces;

interface IErrorHandler
{
    /**
     * Handle an exception thrown in the application.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function handleException(\Throwable $exception): void;

    /**
     * Log an error message.
     *
     * @param string $message
     * @return void
     */
    public function logError(string $message): void;

    /**
     * Perform cleanup or other actions after handling an error.
     *
     * @return void
     */
    public function performPostHandlingActions(): void;
}

