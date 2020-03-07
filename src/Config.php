<?php

namespace Spatie\EventServer;

class Config
{
    public ?string $storagePath = __DIR__ . '/../.storage';

    public string $listenUri = '127.0.0.1:8181';

    /**
     * @return array|\Spatie\EventServer\Domain\Subscriber[]
     */
    public function subscribers(): array
    {
        return [];
    }
}
