<?php

namespace Spatie\EventServer\Tests;

use Spatie\EventServer\Container;
use Spatie\EventServer\Server\Events\EventBus;

class FakeContainer extends Container
{
    public static function make(): FakeContainer
    {
        return new self();
    }

    public function eventBus(): EventBus
    {
        return new class ($this->client()) extends EventBus
        {
            public function trigger(object $event): void
            {
                $this->handle($event);
            }
        };
    }
}
