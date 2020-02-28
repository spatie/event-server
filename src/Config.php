<?php

namespace Spatie\EventServer;

class Config
{
    public ?string $storagePath = __DIR__ . '/../.storage';

    public function routes(): array
    {
        return (new Routes())->routes;
    }
}
