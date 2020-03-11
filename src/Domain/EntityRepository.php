<?php

namespace Spatie\EventServer\Domain;

use Exception;
use Spatie\EventServer\Client\Gateway;

class EntityRepository
{
    private static array $entities = [];

    private string $entityClass;

    public static function for($entity): EntityRepository
    {
        if ($entity instanceof Entity) {
            $entity = $entity->getClass();
        }

        return new self($entity);
    }

    public static function clear(): void
    {
        static::$entities = [];
    }

    public function __construct(string $entityClass)
    {
        $baseEntityClass = Entity::class;

        if ($entityClass === $baseEntityClass) {
            throw new Exception("You cannot directly call methods on {$baseEntityClass}");
        }

        $this->entityClass = $entityClass;
    }

    public function list(): array
    {
        return runOn(
            $server = function () {
                return array_values(static::$entities[$this->entityClass] ?? []);
            },
            $client = function (Gateway $gateway) {
                return $gateway->listEntities($this->entityClass);
            }
        );
    }

    public function find(string $uuid): Entity
    {
        return runOn(
            $server = function () use ($uuid) {
                $entity = static::$entities[$this->entityClass][$uuid] ?? null;

                if (!$entity) {
                    throw new Exception("{$this->entityClass} with id {$uuid} not found");
                }

                return $entity;
            },
            $client = function (Gateway $gateway) use ($uuid) {
                return $gateway->findEntity($this->entityClass, $uuid);
            }
        );
    }

    public function create(Entity $entity): void
    {
        runOn(
            $server = function () use ($entity) {
                static::$entities[$this->entityClass][$entity->uuid] = $entity;
            },
            $client = function (Gateway $gateway) use ($entity) {
                $gateway->createEntity($entity);
            }
        );
    }

    public function delete(Entity $entity): void
    {
        runOn(
            $server = function () use ($entity) {
                unset(static::$entities[$this->entityClass][$entity->uuid]);
            },
            $client = function (Gateway $gateway) use ($entity) {
                $gateway->deleteEntity($entity);
            }
        );
    }
}
