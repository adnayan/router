# Router

```php
<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

define('APP_ROOT', __DIR__);

$router = new \Nayan\Router\Router();
$request = new \Nayan\Router\Request($_GET, $_POST, $_SERVER, getallheaders(), file_get_contents('php://input'));
$response = new \Nayan\Router\Response(new \Nayan\Router\Tests\TwigViewEngineWrapper('src/Tests/Views', []));
$container = new \Nayan\Router\Container\Container();

$container = \Nayan\Router\Tests\AddServices::registerServices($container);
$router = \Nayan\Router\Tests\Routes\ApiRoutes::register($router);

$app = new \Nayan\Router\Application($request, $response, $container);

$app->setErrorHandler(new \Nayan\Router\Tests\ErrorHandler($container->get(\Psr\Log\LoggerInterface::class)));

$app->use(\Nayan\Router\Tests\Middlewares\FooMiddleware::class, []);

$app->register('/api', $router, []);

$app->run();
```

```php
<?php

declare(strict_types=1);

namespace Nayan\Router\Tests;

use Nayan\Router\Router;
use Nayan\Router\Container\IContainer;

use Nayan\Router\Tests\Interfaces\IEggService;
use Nayan\Router\Tests\Interfaces\IHamService;
use Nayan\Router\Tests\Interfaces\ISpamService;

use Psr\Log\LoggerInterface;

use Nayan\Router\Tests\Services\EggService;
use Nayan\Router\Tests\Services\HamService;
use Nayan\Router\Tests\Services\SpamService;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AddServices
{
    public static function registerServices(IContainer $container): IContainer
    {
        $container->registerConfiguration(
            [
                'LOG_FILE' => APP_ROOT . '/logs/' . date('Y-m-d') . '.log'
            ]
        );

        $container->registerSingleton(IEggService::class, EggService::class);

        $container->registerSingleton(LoggerInterface::class, function (IContainer $container) {
            $logger = new Logger('app');
            $logger->pushHandler(new StreamHandler($container->getConfiguration()['LOG_FILE'], Logger::DEBUG));
            return $logger;
        });
        
        $container->registerTransient(IHamService::class, HamService::class);

        $container->registerSingleton(ISpamService::class, function (IContainer $container) {
            return new SpamService($container->get(IEggService::class));
        });

        return $container;
    }
}

```

```php
<?php

declare(strict_types=1);

namespace Nayan\Router\Tests;

use Nayan\Router\Interfaces\IErrorHandler;

use Psr\Log\LoggerInterface;

class ErrorHandler implements IErrorHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handleException(\Throwable $exception): void
    {
        // Log the exception details
        $this->logger->error(
            sprintf(
                "Exception occurred: [%s] %s in %s:%d",
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            )
        );

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
            http_response_code($exception->getCode());
            echo json_encode(['error' => $exception->getMessage()]);
        }
    }

    public function logError(string $message): void
    {
        error_log($message);
        $this->logger->error($message);
    }

    public function performPostHandlingActions(): void
    {
        // Actions like sending notifications or cleaning up resources
    }
}

```

```php
<?php

declare(strict_types=1);

namespace Nayan\Router\Tests\Routes;

class ApiRoutes
{
    public static function register($router)
    {
        $router->map(
            'GET',
            '/hello/:name/foo/:fooId',
            [
                \Nayan\Router\Tests\Controllers\HelloController::class,
                'sayHello'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\BarMiddleware(),
            ]
        );
        
        $router->map(
            'GET',
            '/posts',
            [
                \Nayan\Router\Tests\Controllers\PostController::class,
                'getPosts'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\AuthorizeMiddleware('get_posts'),
                new \Nayan\Router\Tests\Middlewares\LogMiddleware()
            ]
        );

        $router->map(
            'GET',
            '/posts/:postId/comments',
            [
                \Nayan\Router\Tests\Controllers\PostController::class,
                'getComments'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\AuthorizeMiddleware('get_post_comments'),
                new \Nayan\Router\Tests\Middlewares\LogMiddleware()
            ]
        );

        $router->map(
            'GET',
            '/posts/:postId/comments/:commentId',
            [
                \Nayan\Router\Tests\Controllers\PostController::class,
                'getPostCommentById'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\AuthorizeMiddleware('get_post_comment'),
                new \Nayan\Router\Tests\Middlewares\LogMiddleware()
            ]
        );
        
        $router->map(
            'GET',
            '/posts/:postId',
            [
                \Nayan\Router\Tests\Controllers\PostController::class,
                'getPost'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\AuthorizeMiddleware('get_post'),
                new \Nayan\Router\Tests\Middlewares\LogMiddleware()
            ]
        );
        
        $router->map(
            'POST',
            '/posts',
            [
                \Nayan\Router\Tests\Controllers\PostController::class,
                'create'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\AuthorizeMiddleware('create_post'),
                new \Nayan\Router\Tests\Middlewares\LogMiddleware()
            ]
        );
        
        $router->map(
            'PUT',
            '/posts/:postId/comments/:commentId',
            [
                \Nayan\Router\Tests\Controllers\PostController::class,
                'updateComment'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\AuthorizeMiddleware('update_post_comment'),
                new \Nayan\Router\Tests\Middlewares\LogMiddleware()
            ]
        );
        
        $router->map(
            'DELETE',
            '/posts/:postId/comments/:commentId',
            [
                \Nayan\Router\Tests\Controllers\PostController::class,
                'deleteComment'
            ],
            [
                new \Nayan\Router\Tests\Middlewares\AuthorizeMiddleware('delete_post_comment'),
                new \Nayan\Router\Tests\Middlewares\LogMiddleware()
            ]
        );

        return $router;
    }
}

```

```php
<?php

declare(strict_types=1);

namespace Nayan\Router\Tests\Middlewares;

use Nayan\Router\Middleware\Middleware;
use Nayan\Router\Interfaces\IRequest;
use Nayan\Router\Interfaces\IResponse;

class AuthorizeMiddleware extends Middleware
{
    #[\Override]
    public function handle(IRequest $request, callable $next): IResponse
    {
        return $next($request);
    }
}

```
```php
<?php

declare(strict_types=1);

namespace Nayan\Router\Tests\Middlewares;

use Nayan\Router\Middleware\Middleware;
use Nayan\Router\Interfaces\IRequest;
use Nayan\Router\Interfaces\IResponse;

use Nayan\Router\Middleware\MiddlewareHasDependenciesAttribute;

use Nayan\Router\Tests\Interfaces\IEggService;

#[MiddlewareHasDependenciesAttribute([IEggService::class])]
class FooMiddleware extends Middleware
{
    private IEggService $eggService;
    public function __construct(IEggService $eggService)
    {
        $this->eggService = $eggService;
    }
    
    #[\Override]
    public function handle(IRequest $request, callable $next): IResponse
    {
        return $next($request);
    }
}
```
```php
<?php

declare(strict_types=1);

namespace Nayan\Router\Tests\Controllers;

use Nayan\Router\Controller\Controller;

use Nayan\Router\Interfaces\IRequest;
use Nayan\Router\Interfaces\IResponse;

use Nayan\Router\Tests\Interfaces\IEggService;
use Nayan\Router\Tests\Interfaces\IHamService;

class HelloController extends Controller
{
    private IEggService $eggService;
    private IHamService $hamService;

    public function __construct(IHamService $hamService, IEggService $eggService)
    {
        $this->hamService = $hamService;
        $this->eggService = $eggService;
    }

    public function sayHello(IRequest $request, IResponse $response, array $params) : IResponse
    {
        return $response
            ->withStatusCode(200)
            ->withHeader('Content-Type', 'application/json')
            ->withJson([
                'message' => 'Hello, ' . $params['name'] . '!',
                'fooId' => $params['fooId'],
                'hamService' => $this->hamService->getHam(),
                'eggService' => $this->eggService->getEgg()
            ]);
    }
}

```
```php
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```
```php
```