<?php

namespace Spatie\EventServer;

use Spatie\EventServer\Server\Events\DatabaseEventStore;

class Config
{
    public ?string $storagePath = __DIR__ . '/../.storage';

    public string $listenUri = '127.0.0.1:8181';

    public string $eventStore = DatabaseEventStore::class;

    public function databaseConnection(): array
    {
        $path = __DIR__ . '/../.storage/events.sqlite';

        return [
//            'url' => 'sqlite:///:memory:',
            'url' => "sqlite:///{$path}",
        ];
    }

    /**
     * @return array|\Spatie\EventServer\Domain\Subscriber[]
     */
    public function subscribers(): array
    {
        return [];
    }
}
