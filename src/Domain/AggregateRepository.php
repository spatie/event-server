<?php

namespace Spatie\EventServer\Domain;

class AggregateRepository
{
    private array $aggregates = [];

    public function resolve(string $className, string $uuid): AggregateRoot
    {
        return $this->aggregates[$className][$uuid] ??= (new $className($uuid));
    }
}
