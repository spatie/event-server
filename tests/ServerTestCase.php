<?php

namespace Spatie\EventServer\Tests;

use Spatie\EventServer\Container;
use Symfony\Component\Process\Process;

abstract class ServerTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Container::init($this->config);
    }

    public function startServer(bool $log = false): Process
    {
        $process = new Process(['php', __DIR__ . '/testServer.php']);

        $process->start(function ($stream, $output) use ($log) {
            if ($log) {
                fwrite(STDOUT, $output);
            }
        });

        $process->waitUntil(function ($stream, $output) {
            return strpos($output, $this->config->listenUri) !== false;
        });

        return $process;
    }
}
