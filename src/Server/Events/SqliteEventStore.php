<?php

namespace Spatie\EventServer\Server\Events;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Spatie\EventServer\Domain\Event;

class SqliteEventStore extends EventStore
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->ensureEventTableExists();
    }

    public function store(Event $event): void
    {
        $this->connection
            ->insert('stored_events', [
                'uuid' => $event->uuid,
                'timestamp' => time(),
                'serialized_event' => serialize($event),
            ]);
    }

    public function replay(): void
    {
        $statement = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('stored_events')
            ->execute();

        while($row = $statement->fetch()) {
            $event = unserialize($row['serialized_event']);

            $this->eventBus->handle($event, true);
        }
    }

    public function clean(): void
    {
        $this->connection
            ->getSchemaManager()
            ->dropTable('stored_events');

        $this->ensureEventTableExists();
    }

    private function ensureEventTableExists(): void
    {
        $tableExists = $this->connection
            ->getSchemaManager()
            ->tablesExist('stored_events');

        if (! $tableExists) {
            $schema = new Schema();

            $table = $schema->createTable("stored_events");
            $table->addColumn('uuid', 'string');
            $table->setPrimaryKey(['uuid']);
            $table->addColumn('serialized_event', 'text');
            $table->addColumn('timestamp', 'integer');

            $this->connection
                ->getSchemaManager()
                ->createTable($table);
        }
    }
}
