<?php

declare(strict_types=1);

namespace Nayan\Router;

class Router
{
    private $routes;

    public function __construct()
    {
        $this->routes = array();
    }

    public function map(string $method, string $path, $handler, $middleware): void
    {
        $this->routes[] = new Route($method, $path, $handler, $middleware);
    }

    public function append(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function findMatch(string $requestMethod, string $requestUrl) : ?Route
    {
        return array_reduce($this->routes, function ($carry, $route) use ($requestMethod, $requestUrl) { return $carry ?? $route->match($requestMethod, $requestUrl); }, null);
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
