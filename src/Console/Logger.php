<?php

namespace Spatie\EventServer\Console;

use Carbon\Carbon;
use Symfony\Component\Console\Output\OutputInterface;

class Logger
{
    private OutputInterface $output;

    private ?string $prefix = null;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function log(string $line): void
    {
        $timestamp = Carbon::now()->toDateTimeString();

        $prefix = $this->prefix
            ? "[$this->prefix] "
            : '';

        $this->output->writeln("[$timestamp] {$prefix}{$line}");
    }

    public function prefix(string $prefix): self
    {
        $clone = clone $this;

        $clone->prefix = $prefix;

        return $clone;
    }

    public function error(string $line): void
    {
        $this->log("<error>{$line}</error>");
    }

    public function info(string $line): void
    {
        $this->log("<info>{$line}</info>");
    }

    public function comment(string $line): void
    {
        $this->log("<comment>{$line}</comment>");
    }
}
