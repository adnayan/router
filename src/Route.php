<?php

declare(strict_types=1);

namespace Nayan\Router;

class Route
{
    private $method;
    private $url;
    private $handler;
    private $middlewares;
    private $params;

    public function __construct(string $method, string $url, array $handler, array $middlewares)
    {
        $this->method = $method;
        $this->url = $url;
        $this->handler = $handler;
        $this->middlewares = $middlewares;
        $this->params = array();
    }

    public function match(string $method, string $path)
    {
        if ($method === $this->method)
        {
            $pattern = preg_replace(
                '/\\\:[a-zA-Z0-9\_\-]+/',
                '([a-zA-Z0-9\-\_]+)',
                preg_quote($this->url)
            );

            if (preg_match("@^$pattern$@D", $path, $matches))
            {
                array_shift($matches);
        
                if (count($matches))
                {
                    preg_match_all('/\:[a-zA-Z0-9\_\-]+/', $this->url, $paramMatches);
                    $paramNames = array_map(fn($param) => str_replace(':', '', $param), $paramMatches[0]);

                    $this->params = array_combine($paramNames, $matches);
                }
                return $this;
            }
        }
        
        return null;
    }

    public function addPrefix(string $url)
    {
        $this->url = sprintf('%s%s', $url, $this->url);
        return $this;
    }

    public function addMiddlewares(array $middlewares)
    {
        $this->middlewares = [...$this->middlewares, ...$middlewares];

        return $this;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getParams()
    {
        return $this->params;
    }
}
