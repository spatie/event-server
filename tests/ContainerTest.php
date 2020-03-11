<?php

namespace Spatie\EventServer\Tests;

use Exception;
use Spatie\EventServer\Config;
use Spatie\EventServer\Container;

class ContainerTest extends TestCase
{
    /** @test */
    public function test_autowire()
    {
        $container = Container::init(new Config());

        $instance = $container->resolve(InstanceA::class);

        $this->assertInstanceOf(InstanceA::class, $instance);
    }

    /** @test */
    public function autowire_fails_without_type()
    {
        $container = Container::init(new Config());

        $this->expectException(Exception::class);

        $container->resolve(InstanceWithMissingType::class);
    }
}

class InstanceA
{
    private InstanceB $instanceB;

    public function __construct(InstanceB $instanceB)
    {
        $this->instanceB = $instanceB;
    }
}

class InstanceB
{
    private InstanceC $instanceC;

    public function __construct(InstanceC $instanceC)
    {
        $this->instanceC = $instanceC;
    }
}

class InstanceC
{
}

class InstanceWithMissingType
{
    private $instance;

    public function __construct($instance)
    {
        $this->instance = $instance;
    }
}
