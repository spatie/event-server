<?php

namespace Spatie\EventServer\Domain;

class AggregateRepository
{
    private array $aggregates = [];

    public function resolve(string $className, string $uuid): Aggregate
    {
        return $this->aggregates[$className][$uuid] ??= (new $className($uuid));
    }
}
