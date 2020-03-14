<?php

namespace Spatie\EventServer;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Exception;
use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Socket\Connector;
use ReflectionClass;
use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Client\SocketClient;
use Spatie\EventServer\Console\Commands\ClientCommand;
use Spatie\EventServer\Console\Commands\ServerCommand;
use Spatie\EventServer\Console\Commands\SocketCommand;
use Spatie\EventServer\Console\ConsoleApplication;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Domain\AggregateRepository;
use Spatie\EventServer\Domain\Subscribers;
use Spatie\EventServer\Server\Events\EventBus;
use Spatie\EventServer\Server\Events\EventStore;
use Spatie\EventServer\Server\Events\FileEventStore;
use Spatie\EventServer\Server\Events\SqliteEventStore;
use Spatie\EventServer\Server\Server;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Container
{
    private static ?self $instance = null;

    private array $singletons = [];

    public static bool $isServer = false;

    protected Config $config;

    public static function isServer(): bool
    {
        return static::$isServer;
    }

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

    public function resolve(string $fqcn): object
    {
        $className = (new ReflectionClass($fqcn))->getShortName();

        $resolverMethod = lcfirst($className);

        if (method_exists($this, $resolverMethod)) {
            return $this->$resolverMethod();
        }

        $autowire = $this->autowire($fqcn);

        if ($autowire) {
            return $autowire;
        }

        throw new Exception("No container definition found for {$className}");
    }

    protected function autowire(string $fqcn): ?object
    {
        $reflectionClass = new ReflectionClass($fqcn);

        if (! $reflectionClass->hasMethod('__construct')) {
            return new $fqcn;
        }

        $reflectionConstructor = $reflectionClass->getMethod('__construct');

        $arguments = [];

        foreach ($reflectionConstructor->getParameters() as $reflectionParameter) {
            $parameterType = $reflectionParameter->getType();

            if (! $parameterType) {
                throw new Exception("Could not autowire property {$reflectionParameter->getName()} in class {$reflectionClass->getName()}, its type is missing");
            }

            $arguments[] = $this->resolve($parameterType->getName());
        }

        return new $fqcn(...$arguments);
    }

    public function singleton(
        string $class,
        Closure $createInstance,
        ?Closure $afterCreated = null
    ) {
        if (! isset($this->singletons[$class])) {
            $instance = $createInstance();

            $this->singletons[$class] = $instance;

            if ($afterCreated) {
                $afterCreated($instance);
            }
        }

        return $this->singletons[$class];
    }

    public function loop(): LoopInterface
    {
        return $this->singleton(LoopInterface::class, fn() => EventLoopFactory::create());
    }

    public function subscribers(): Subscribers
    {
        return $this->singleton(Subscribers::class, fn() => new Subscribers(
            $this->config->subscribers(),
        ));
    }

    public function server(): Server
    {
        return $this->singleton(Server::class, fn() => new Server(
            $this->loop(),
            $this->logger(),
            $this->eventStore(),
            $this->config->listenUri,
        ));
    }

    public function gateway(): Gateway
    {
        return $this->singleton(Gateway::class, fn() => new Gateway(
            $this->socketClient(),
        ));
    }

    public function socketClient(): SocketClient
    {
        return new SocketClient(
            $this->loop(),
            $this->socketConnector(),
            $this->config->listenUri,
        );
    }

    public function socketConnector(): Connector
    {
        return new Connector($this->loop());
    }

    public function logger(): Logger
    {
        return $this->singleton(Logger::class, fn() => new Logger(
            $this->consoleOutput(),
        ));
    }

    public function consoleApplication(): ConsoleApplication
    {
        $application = new ConsoleApplication();

        /** @var \SplFileInfo[] $commands */
        $commands = Finder::create()->in(__DIR__ . '/Console/Commands')->name('*Command.php');

        foreach ($commands as $commandClass) {
            $className = "\\Spatie\\EventServer\\Console\\Commands\\{$commandClass->getBasename('.php')}";

            $command = $this->resolve($className);

            $application->addCommands([$command]);
        }

        return $application;
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
            $this->aggregateRepository(),
            $this->subscribers(),
        ));
    }

    public function eventStore(): EventStore
    {
        return $this->singleton(
            EventStore::class,
            fn() => $this->resolve($this->config->eventStore),
            fn(EventStore $eventStore) => $eventStore->setEventBus($this->eventBus())
        );
    }

    public function sqliteConnection(): Connection
    {
        return DriverManager::getConnection($this->config->databaseConnection());
    }

    public function fileEventStore(): FileEventStore
    {
        return new FileEventStore(
            $this->config->storagePath
        );
    }

    public function sqliteEventStore(): SqliteEventStore
    {
        return new SqliteEventStore(
            $this->sqliteConnection()
        );
    }

    public function aggregateRepository(): AggregateRepository
    {
        return $this->singleton(AggregateRepository::class, fn() => new AggregateRepository());
    }
}
