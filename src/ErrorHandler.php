<?php

declare(strict_types=1);

namespace Nayan\Router;

use Nayan\Router\Interfaces\IErrorHandler;

class ErrorHandler implements IErrorHandler
{
    public function handleException(\Throwable $exception): void
    {
        // Log the exception details
        error_log(
            sprintf(
                "Exception occurred: [%s] %s in %s:%d",
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            )
        );

        // Display a generic error message to the user (for production)
        if (php_sapi_name() !== 'cli')
        {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }

    public function logError(string $message): void
    {
        error_log($message);
    }

    public function performPostHandlingActions(): void
    {
        // Actions like sending notifications or cleaning up resources
    }
}
