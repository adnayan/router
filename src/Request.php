<?php

declare(strict_types=1);

namespace Nayan\Router;

use Nayan\Router\Interfaces\IRequest;

class Request implements IRequest
{
    protected $method;
    protected $queryParams;
    protected $postParams;
    protected $serverParams;
    protected $headers;
    protected $body;

    public function __construct(array $queryParams = [], array $postParams = [], array $serverParams = [], array $headers = [], string $body = '')
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->queryParams = $queryParams;
        $this->postParams = $postParams;
        $this->serverParams = $serverParams;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }

    public function getQuery(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function getQueries(): array
    {
        return $this->queryParams;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getServer(string $key)
    {
        return $this->serverParams[$key] ?? null;
    }
}
