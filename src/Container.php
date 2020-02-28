<?php

namespace Spatie\EventServer;

use Closure;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use Spatie\EventServer\Client\Client;
use Spatie\EventServer\Console\Commands\ClientCommand;
use Spatie\EventServer\Console\Commands\ServerCommand;
use Spatie\EventServer\Console\ConsoleApplication;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Server\Events\EventBus;
use Spatie\EventServer\Server\RequestHandlers\EventRequestHandler;
use Spatie\EventServer\Server\Router;
use Spatie\EventServer\Server\Server;
use Spatie\EventServer\Tests\FakeContainer;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use function FastRoute\simpleDispatcher;

class Container
{
    private static ?self $instance = null;

    private static array $singletons = [];

    public static function make(): self
    {
        return static::$instance ??= new self();
    }

    public static function fake(Container $container): self
    {
        return static::$instance = $container;
    }

    private function singleton(string $class, Closure $closure)
    {
        if (! isset(static::$singletons[$class])) {
            static::$singletons[$class] = $closure();
        }

        return static::$singletons[$class];
    }

    public function eventBus(): EventBus
    {
        return $this->singleton(EventBus::class, fn() => new EventBus(
            $this->client(),
        ));
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
        ));
    }

    public function client(): Client
    {
        return $this->singleton(Client::class, fn () => new Client());
    }

    public function router(): Router
    {
        return $this->singleton(Router::class, fn() => new Router(
            $this->dispatcher(),
        ));
    }

    public function dispatcher(): Dispatcher
    {
        return $this->singleton(
            Dispatcher::class,
            function () {
                return simpleDispatcher(function (RouteCollector $routeCollector) {
                    $routeCollector->addRoute('POST', '/events', EventRequestHandler::class);
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
                new ClientCommand(),
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
}
