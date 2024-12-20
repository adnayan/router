# Container

This document explains how container should be implemented.

```php

$configurations = [
    'baz' => [
        'key' => 'value',
        'key2' => 'value2',
        'key3' => 'value3'
    ]
];

interface IFooService
{
    public function getFoo();
}

interface IBarService
{
    public function getBar();
}

interface IFooBarService
{
    public function getFooBar();
}

class FooService implements IFooService
{
    public function getFoo()
    {
        return 'Foo';
    }
}

class BarService implements IBarService
{
    public function getBar()
    {
        return 'Bar';
    }
}

class FooBarService implements IFooBarService
{
    private $fooService;
    private $barService;

    public function __construct(IFooService $fooService, IBarService $barService)
    {
        $this->fooService = $fooService;
        $this->barService = $barService;
    }

    public function getFooBar()
    {
        return $this->fooService->getFoo() . ' ' . $this->barService->getBar();
    }
}

class BazService
{
    private $bazConfiguration;

    public function __construct(array $bazConfiguration)
    {
        $this->bazConfiguration = $bazConfiguration;
    }

    public function getBaz()
    {
        return $this->bazConfiguration['key'] . ' ' . $this->bazConfiguration['key2'] . ' ' . $this->bazConfiguration['key3'];
    }
}

class EggService
{
    private $fooService;
    private $barService;

    public function __construct(IFooService $fooService, IBarService $barService)
    {
        $this->fooService = $fooService;
        $this->barService = $barService;
    }
}

interface IServiceOne
{
    public function getOne() : string;
}

interface IServiceTwo
{
    public function getTwo() : string;
}

$container = new Container();
$container->registerConfiguration($configurations);

$container->registerSingleton(IFooService::class, FooService::class);
$container->registerSingleton(IBarService::class, BarService::class);
$container->registerTransient(IFooBarService::class, FooBarService::class);
$container->registerSingleton(BazConfiguration::class, BazConfiguration::class);
$container->registerTransient(BazService::class, function (Container $container) {
    return new BazService($container->getConfiguration()['baz']);
});
$container->registerSingleton(EggService::class, function (Container $container) {
    return new EggService($container->get(IFooService::class), $container->get(IBarService::class));
});

$container->registerSingleton(IServiceOne::class, ServiceOne::class);
$container->registerSingleton(IServiceTwo::class, ServiceTwo::class);

$fooBarService = $container->get(IFooBarService::class);
echo $fooBarService->getFooBar() . PHP_EOL;

$bazService = $container->get(BazService::class);
echo $bazService->getBaz() . PHP_EOL;

```
