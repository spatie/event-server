<?php

namespace Spatie\EventServer\Console;

use Symfony\Component\Console\Application;

class ConsoleApplication extends Application
{
    public function __construct()
    {
        parent::__construct('EventServer', '0.1');
    }
}
