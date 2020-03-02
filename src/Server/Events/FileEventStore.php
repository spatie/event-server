<?php

namespace Spatie\EventServer\Server\Events;

use Carbon\Carbon;
use Exception;
use Spatie\EventServer\Domain\Event;

class FileEventStore extends EventStore
{
    private string $storagePath;

    public function __construct(?string $storagePath)
    {
        if ($storagePath === null) {
            throw new Exception('No storage path configured');
        }

        $this->storagePath = $storagePath;
    }

    public function store(Event $event): void
    {
        $fileName = implode('/', [
            $this->storagePath,
            Carbon::now()->format('Y-m-d_H:i:s') . '_' . ($event->uuid ?? uuid()),
        ]);

        file_put_contents($fileName, serialize($event));
    }

    public function replay(): void
    {
        $files = array_filter((array) glob("{$this->storagePath}/*"));

        foreach ($files as $file) {
            $event = unserialize(file_get_contents($file));

            $this->eventBus->handle($event, true);
        }
    }

    public function clean(): void
    {
        array_map(
            'unlink',
            array_filter((array) glob("{$this->storagePath}/*"))
        );
    }
}
