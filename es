#!/usr/bin/env php

<?php

use App\Config;
use App\Container;

require_once __DIR__ . '/vendor/autoload.php';

$container = Container::init(new Config());

$container
    ->consoleApplication()
    ->run(
        $container->consoleInput(),
        $container->consoleOutput()
    );
