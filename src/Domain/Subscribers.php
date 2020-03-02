<?php

namespace Spatie\EventServer\Domain;

use Iterator;

class Subscribers implements Iterator
{
    public array $subscribers = [];

    private int $position = 0;

    public function __construct(array $subscribers = [])
    {
        $this->subscribers = $subscribers;
    }

    public function add(Subscriber $className): self
    {
        $this->subscribers[] = $className;

        return $this;
    }

    public function current(): Subscriber
    {
        return $this->subscribers[$this->position];
    }

    public function next(): void
    {
        $this->position += 1;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->subscribers);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
