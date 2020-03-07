<?php

namespace Spatie\EventServer\Tests\Domain;

use Spatie\EventServer\Domain\Entity;
use Spatie\EventServer\Tests\TestCase;

class EntityTest extends TestCase
{
    /** @test */
    public function test_create_and_find()
    {
        $entity = TestEntity::create([
            'name' => 'a'
        ]);

        $savedEntity = TestEntity::find($entity->uuid);

        $this->assertTrue($entity->equals($savedEntity));
    }

    /** @test */
    public function test_list()
    {
        $entity = TestEntity::create([
            'name' => 'a'
        ]);

        $savedEntities = TestEntity::list();

        $this->assertCount(1, $savedEntities);
        $this->assertTrue($entity->equals($savedEntities[0]));
    }

    /** @test */
    public function test_delete()
    {
        $entity = TestEntity::create([
            'name' => 'a'
        ]);

        $entity->delete();

        $savedEntities = TestEntity::list();

        $this->assertCount(0, $savedEntities);
    }
}

class TestEntity extends Entity
{
    public string $name;
}
