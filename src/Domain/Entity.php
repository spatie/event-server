<?php

namespace Spatie\EventServer\Domain;

abstract class Entity
{
    public string $uuid;

    /**
     * @param string $uuid
     *
     * @return \Spatie\EventServer\Domain\Entity|static
     */
    public static function find(string $uuid): Entity
    {
        return EntityRepository::for(static::class)->find($uuid);
    }

    /**
     * @return \Spatie\EventServer\Domain\Entity[]|static[]
     */
    public static function list(): array
    {
        return EntityRepository::for(static::class)->list();
    }

    /**
     * @param array $data
     *
     * @return \Spatie\EventServer\Domain\Entity|static
     */
    public static function create(array $data): Entity
    {
        $entity = new static($data);

        EntityRepository::for(static::class)->create($entity);

        return $entity;
    }

    public function __construct(array $data = [])
    {
        if (! isset($data['uuid'])) {
            $data['uuid'] = uuid();
        }

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getClass(): string
    {
        return static::class;
    }

    public function delete(): void
    {
        EntityRepository::for(static::class)->delete($this);
    }

    public function equals(Entity $other): bool
    {
        return $this->uuid === $other->uuid;
    }
}
