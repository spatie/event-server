<?php

namespace Spatie\EventServer\Server\RequestHandlers\Entities;

use Spatie\EventServer\Domain\EntityRepository;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestHandlers\RequestHandler;
use Spatie\EventServer\Server\RequestPayload;

class DeleteEntityHandler implements RequestHandler
{
    public function __invoke(RequestPayload $payload): Payload
    {
        /** @var \Spatie\EventServer\Domain\Entity $entity */
        $entity = $payload->get('entity');

        $repository = EntityRepository::for($entity);

        $repository->delete($entity);

        return new Payload();
    }
}
