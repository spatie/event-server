<?php

namespace Spatie\EventServer\Tests;

use Carbon\Carbon;
use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Container;
use Spatie\EventServer\Server\Server;
use Spatie\EventServer\Tests\Fakes\TestConfig;
use Spatie\EventServer\Tests\Fakes\TestContainer;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected TestContainer $container;

    protected TestConfig $config;

    protected Server $server;

    protected Gateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2020-01-01 00:00:00');

        $this->config = new TestConfig();
        $this->container = TestContainer::init($this->config);
        $this->server = $this->container->server();
        $this->gateway = $this->container->gateway();

        if (! is_dir($this->config->storagePath)) {
            mkdir($this->config->storagePath);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_dir($this->config->storagePath)) {
            $this->container->eventStore()->clean();

            rmdir($this->config->storagePath);
        }
    }

    protected function assertEventStored($event)
    {
        $timestamp = Carbon::now()->format('Y-m-d_H:i:s');

        $name = '';

        if (is_string($event)) {
            $name = $event;
        } elseif (is_object($event)) {
            $name = $event->uuid ?? '';
        }

        if (strpos($name, $timestamp) === false) {
            $name = "{$timestamp}_{$name}";
        }

        $filename = "{$this->config->storagePath}/{$name}";

        $this->assertTrue(
            file_exists($filename),
            "Failed to assert that event was stored in {$filename}"
        );
    }

    protected function storeEvents(object ...$events)
    {
        $eventStore = $this->container->eventStore();

        foreach ($events as $event) {
            $eventStore->store($event);
        }
    }
}
