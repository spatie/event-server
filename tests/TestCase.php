<?php

namespace Spatie\EventServer\Tests;

use Spatie\EventServer\Container;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Container::fake(new FakeContainer());
    }
}
