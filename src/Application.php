<?php

declare(strict_types=1);

namespace Nayan\Router;

use Exception;

use Nayan\Router\ErrorHandler;

use Nayan\Router\Interfaces\IRequest;
use Nayan\Router\Interfaces\IResponse;
use Nayan\Router\Interfaces\IErrorHandler;
use Nayan\Router\Interfaces\IPipelineProcess;
use Nayan\Router\Container\IContainer;

use Nayan\Router\Middleware\IMiddleware;
use Nayan\Router\Middleware\MiddlewareHasDependenciesAttribute;

use Nayan\Router\Errors\InvalidRequestUrlError;
use Nayan\Router\Errors\ControllerNotFoundError;
use Nayan\Router\Errors\ControllerMethodNotFoundError;
use Nayan\Router\Errors\ViewEngineNotFoundError;

/**
 * Class Application
 * This class is the main class of the framework. It is responsible for handling the request and response.
 *
 * @package Nayan\Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */

class Application
{
    private Router $router;
    private IRequest $request;
    private IResponse $response;
    private IContainer $container;
    private array $middlewares;
    private IErrorHandler $errorHandler;

    /**
     * Application constructor.
     *
     * This constructor accepts the request, response, and container objects and initializes the router and middlewares.
     *
     * @param IRequest $request The request object.
     * @param IResponse $response The response object.
     * @param IContainer $container The container object.
     */
    public function __construct(
        IRequest $request,
        IResponse $response,
        IContainer $container
    )
    {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
        $this->router = new Router();
        $this->middlewares = [];
        $this->errorHandler = new ErrorHandler();
    }

    public function setErrorHandler(IErrorHandler $errorHandler): void
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * This method registers the routes of the router object to the application.
     *
     * @param string $url The URL to register the routes to.
     * @param Router $router The router object.
     * @param array $middlewares The middlewares to apply to the routes.
     */
    public function register(string $url, Router $router, array $middlewares = []): void
    {
        foreach ($router->getRoutes() as $route)
        {
            $route->addPrefix(($this->config["BASE_FOLDER"] ?? '') . $url)
                  ->addMiddlewares($middlewares);

            $this->router->append($route);
        }
    }

    /**
     * This method runs the application.
     */
    public function run(): void
    {
        try
        {
            $match = $this->findMatch();

            $controller = $this->getController($match);

            $pipeline = $this->createPipeline($controller, $match);

            $this->response = $pipeline($this->request);

            echo $this->response->respond();
        }
        catch (
            MiddlewareError |
            ControllerNotFoundError |
            ControllerMethodNotFoundError |
            InvalidRequestUrlError |
            ViewEngineNotFoundError |
            Exception $e
        )
        {
            if ($this->errorHandler != null)
            {
                $this->errorHandler->handleException($e);
                $this->errorHandler->performPostHandlingActions();
            }
        }
    }

    private function findMatch()
    {
        $match = $this->router->findMatch($this->request->getServer('REQUEST_METHOD') ?? "GET", $this->request->getServer('PATH_INFO') ?? "/");

        if ($match == null)
        {
            throw new InvalidRequestUrlError($_SERVER['REQUEST_URI']);
        }

        if (!class_exists($match->getHandler()[0]))
        {
            throw new ControllerNotFoundError("Class '" . $match->getHandler()[0] . "' not found.");
        }

        return $match;
    }

    private function getController($match)
    {
        $reflection = new \ReflectionClass($match->getHandler()[0]);
        $constructor = $reflection->getConstructor();

        if ($constructor == null)
        {
            return $reflection->newInstance();
        }

        $params = $constructor->getParameters();
        $dependencies = [];

        foreach ($params as $param)
        {
            $dependencies[] = $this->container->get($param->getType()->getName());
        }

        $controller = $reflection->newInstanceArgs($dependencies);

        if (is_null($controller))
        {
            throw new ControllerNotFoundError("Controller '" . $match->getHandler()[0] . "' not found.");
        }

        if (!method_exists($controller, $match->getHandler()[1]))
        {
            throw new ControllerMethodNotFoundError("Method '" . $match->getHandler()[1] . "' not found in class '" . $match->getHandler()[0] . "'.");
        }

        return $controller;
    }

    private function createPipeline($controller, $match)
    {
        $pipeline = function (IRequest $request) use ($controller, $match) {
            return $controller->{$match->getHandler()[1]}($request, $this->response, [...$request->getQueries(), ...$match->getParams()]);
        };

        $middlewares = [...$this->middlewares, ...$match->getMiddlewares()];

        foreach (array_reverse($middlewares) as $middleware)
        {
            $pipeline = function (IRequest $request) use ($middleware, $pipeline) {
                return $middleware->handle($request, $pipeline);
            };
        }

        return $pipeline;
    }

    /**
     * This method adds a middleware to the application.
     *
     * @param string $middleware The middleware to add.
     * @param array $args The arguments to pass to the middleware.
     */
    public function use(string $middleware, $args = []): void
    {
        $dependencyInstances = [];

        $reflection = new \ReflectionClass($middleware);

        if (
            count($reflection->getAttributes()) &&
            count($reflection->getAttributes()[0]->getArguments()) &&
            count($reflection->getAttributes()[0]->getArguments()[0])
        )
        {
            foreach ($reflection->getAttributes() as $attribute)
            {
                if (
                    $attribute->getName() === MiddlewareHasDependenciesAttribute::class &&
                    count($attribute->getArguments()) &&
                    count($attribute->getArguments()[0])
                )
                {
                    foreach ($attribute->getArguments()[0] as $dependency)
                    {
                        $dependencyInstances[] = $this->container->get($dependency);
                    }
                }
            }

        }

        $dependencyInstances = [...$dependencyInstances, ...$args];

        $this->middlewares[] = new $middleware(...$dependencyInstances);
    }
}
