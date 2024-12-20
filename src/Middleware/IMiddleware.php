<?php

declare(strict_types=1);

namespace Nayan\Router\Middleware;

use Nayan\Router\Interfaces\IRequest;
use Nayan\Router\Interfaces\IResponse;
use Nayan\Router\Container\IContainer;

/**
 * Interface IMiddleware
 *
 * This interface is the base interface for all middlewares.
 *
 * @package Router
 * @author Nayan Adhikari
 * @version 1.0.0
 * @license MIT
 * @link
 * @see
 */
interface IMiddleware
{
    /**
     * This method handles the request and returns the response.
     *
     * @param IRequest $request The request object.
     * @param callable $next The next middleware.
     * @return IResponse The response object.
     */
    public function handle(IRequest $request, callable $next): IResponse;
}
