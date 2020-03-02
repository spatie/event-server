<?php

namespace Spatie\EventServer\Domain;

class EventMeta
{
    public ?string $aggregateClass = null;

    public ?string $aggregateUuid = null;
}
