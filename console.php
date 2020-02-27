<?php

use Spatie\EventServer\Container;

require_once __DIR__ . '/vendor/autoload.php';

$container = new Container();

$container->loop();
$container->loop();
$container->loop();
$container->loop();

$container
    ->consoleApplication()
    ->run(
        $container->consoleInput(),
        $container->consoleOutput()
    );
