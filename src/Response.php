<?php

declare(strict_types=1);

namespace Nayan\Router;

use Nayan\Router\Interfaces\IResponse;
use Nayan\Router\Interfaces\IViewEngineWrapper;

use Nayan\Router\Errors\ViewEngineNotFoundError;

class Response implements IResponse
{
    protected $content;
    protected $statusCode;
    protected $headers;
    protected $responseType;
    protected ?IViewEngineWrapper $viewEngineWrapper;
    protected string $viewFile;

    public function __construct(?IViewEngineWrapper $viewEngineWrapper = null)
    {
        $this->content = '';
        $this->statusCode = 200;
        $this->headers = [];
        $this->responseType = 'text/html';
        $this->viewEngineWrapper = $viewEngineWrapper;
        $this->viewFile = '';
    }

    public function withView(string $view, array $content = []): IResponse
    {
        $this->viewFile = $view;
        $this->content = $content;

        return $this;
    }

    public function withJson(mixed $content): IResponse
    {
        $this->content = json_encode($content);

        $this->responseType = 'application/json';

        return $this;
    }

    public function withStatusCode(int $statusCode): IResponse
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function withHeader(string $name, string $value): IResponse
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeader(string $name): string
    {
        return $this->headers[$name] ?? "";
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function respond() : string
    {
        foreach ($this->headers as $headerName => $headerValue)
        {
            header($headerName . ": " . $headerValue);
        }

        http_response_code($this->statusCode);

        if ($this->responseType == 'application/json')
        {
            return $this->content;
        }
        else
        {
            if ($this->viewEngineWrapper)
            {
                return $this->viewEngineWrapper->render($this->viewFile, $this->content);
            }
            else
            {
                throw new ViewEngineNotFoundError('No view specified.');
            }
        }
    }
}
