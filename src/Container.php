<?php

namespace Spatie\EventServer;

use Closure;
use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use ReflectionClass;
use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Console\Commands\ClientCommand;
use Spatie\EventServer\Console\Commands\ServerCommand;
use Spatie\EventServer\Console\ConsoleApplication;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Domain\AggregateRepository;
use Spatie\EventServer\Server\Events\EventBus;
use Spatie\EventServer\Server\Events\EventStore;
use Spatie\EventServer\Server\Events\FileEventStore;
use Spatie\EventServer\Server\RequestHandlers\GetAggregateHandler;
use Spatie\EventServer\Server\RequestHandlers\TriggerEventHandler;
use Spatie\EventServer\Server\Router;
use Spatie\EventServer\Server\Server;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function FastRoute\simpleDispatcher;

class Container
{
    private static ?self $instance = null;

    private static array $singletons = [];

    private Config $config;

    /**
     * @param \Spatie\EventServer\Config $config
     *
     * @return static
     */
    public static function init(Config $config)
    {
        return static::$instance = (new static($config));
    }

    public static function make(): self
    {
        return static::$instance;
    }

    private function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function resolve(string $class): object
    {
        $className = (new ReflectionClass($class))->getShortName();

        $resolverMethod = lcfirst($className);

        if (method_exists($this, $resolverMethod)) {
            return $this->$resolverMethod();
        }

        throw new Exception("No container definition found for {$class}");
    }

    public function singleton(
        string $class,
        Closure $createInstance,
        ?Closure $afterCreated = null
    ) {
        if (! isset(static::$singletons[$class])) {
            $instance = $createInstance();

            static::$singletons[$class] = $instance;

            if ($afterCreated) {
                $afterCreated($instance);
            }
        }

        return static::$singletons[$class];
    }

    public function loop(): LoopInterface
    {
        return $this->singleton(LoopInterface::class, fn() => EventLoopFactory::create());
    }

    public function server(): Server
    {
        return $this->singleton(Server::class, fn() => new Server(
            $this->loop(),
            $this->router(),
            $this->logger(),
            $this->eventStore()
        ));
    }

    public function gateway(): Gateway
    {
        return $this->singleton(Gateway::class, fn() => new Gateway(
            $this->httpClient(),
        ));
    }

    public function httpClient(): HttpClientInterface
    {
        return $this->singleton(HttpClientInterface::class, fn() => HttpClient::create());
    }

    public function router(): Router
    {
        return $this->singleton(Router::class, fn() => new Router(
            $this,
            $this->dispatcher(),
        ));
    }

    public function dispatcher(): Dispatcher
    {
        return $this->singleton(
            Dispatcher::class,
            function () {
                return simpleDispatcher(function (RouteCollector $routeCollector) {
                    foreach ($this->config->routes() as $route) {
                        $routeCollector->addRoute(...$route);
                    }
                });
            }
        );
    }

    public function logger(): Logger
    {
        return $this->singleton(Logger::class, fn() => new Logger(
            $this->consoleOutput(),
        ));
    }

    public function consoleApplication(): ConsoleApplication
    {
        return $this->singleton(ConsoleApplication::class, function () {
            $application = new ConsoleApplication();

            $application->addCommands([
                new ServerCommand($this->server()),
                new ClientCommand($this->logger()),
            ]);

            return $application;
        });
    }

    public function consoleInput(): InputInterface
    {
        return $this->singleton(InputInterface::class, fn() => new ArgvInput());
    }

    public function consoleOutput(): OutputInterface
    {
        return $this->singleton(OutputInterface::class, fn() => new ConsoleOutput());
    }

    public function eventBus(): EventBus
    {
        return $this->singleton(EventBus::class, fn() => new EventBus(
            $this->gateway(),
            $this->aggregateRepository()
        ));
    }

    public function eventStore(): EventStore
    {
        return $this->singleton(
            EventStore::class,
            fn() => new FileEventStore(
                $this->config->storagePath
            ),
            fn(EventStore $eventStore) => $eventStore->setEventBus($this->eventBus())
        );
    }

    public function triggerEventHandler(): TriggerEventHandler
    {
        return $this->singleton(TriggerEventHandler::class, fn() => new TriggerEventHandler(
            $this->eventBus(),
            $this->eventStore()
        ));
    }

    public function getAggregateHandler(): GetAggregateHandler
    {
        return $this->singleton(GetAggregateHandler::class, fn() => new GetAggregateHandler(
            $this->aggregateRepository()
        ));
    }

    public function aggregateRepository(): AggregateRepository
    {
        return $this->singleton(AggregateRepository::class, fn() => new AggregateRepository());
    }
}
