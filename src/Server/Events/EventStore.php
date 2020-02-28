<?php

namespace Spatie\EventServer\Server\Events;

use Carbon\Carbon;
use Exception;
use Spatie\EventServer\Domain\Event;

class EventStore
{
    private string $storagePath;

    private EventBus $eventBus;

    public function __construct(?string $storagePath)
    {
        if ($storagePath === null) {
            throw new Exception('No storage path configured');
        }

        $this->storagePath = $storagePath;
    }

    public function setEventBus(EventBus $eventBus): self
    {
        $this->eventBus = $eventBus;

        return $this;
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

            $this->eventBus->handle($event);
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
