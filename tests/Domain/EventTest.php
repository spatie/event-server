<?php

namespace Spatie\EventServer\Tests\Domain;

use Spatie\EventServer\Container;
use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Server\Events\EventStore;
use Spatie\EventServer\Tests\TestCase;

class EventTest extends TestCase
{
    private string $snapshotFile = '2020-01-01_00:00:00_063e9b93-af33-40f1-8228-e0cc02ccc096';

    private string $snapshotContents = 'O:41:"Spatie\EventServer\Tests\Domain\TestEvent":1:{s:3:"foo";s:1:"a";}';

    private EventStore $eventStore;

    protected function setUp(): void
    {
        parent::setUp();

        file_put_contents(
            "{$this->config->storagePath}/{$this->snapshotFile}",
            $this->snapshotContents
        );

        $this->eventStore = Container::make()->eventStore();
    }

    /** @test */
    public function restore_event_which_class_has_been_updated()
    {
        $this->eventStore->replay();
    }
}

class TestEvent extends Event
{
    public string $bar;

    public function __construct(string $bar)
    {
        $this->bar = $bar;
    }

    public function __unserialize(array $data): void
    {
        if (isset($data['foo'])) {
            $data['bar'] = $data['foo'];
            unset($data['foo']);
        }

        parent::__unserialize($data);
    }
}
