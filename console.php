<?php

use Spatie\EventServer\Config;
use Spatie\EventServer\Container;

require_once __DIR__ . '/vendor/autoload.php';

$container = Container::init(new Config());

$container
    ->consoleApplication()
    ->run(
        $container->consoleInput(),
        $container->consoleOutput()
    );
