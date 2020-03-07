<?php

use App\Container;
use Spatie\EventServer\Config;

require_once __DIR__ . '/vendor/autoload.php';

$container = Container::init(new Config());

$container
    ->consoleApplication()
    ->run(
        $container->consoleInput(),
        $container->consoleOutput()
    );
