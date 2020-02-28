<?php

namespace Spatie\EventServer\Tests;

use Spatie\EventServer\Container;

class ContainerTest extends TestCase
{
    /** @test */
    public function test()
    {
        $container = new Container();

        $container->loop();
        $container->loop();
        $container->loop();

        dd($container);
    }
}
