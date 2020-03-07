<?php

namespace Spatie\EventServer\Server\RequestHandlers\Entities;

use Spatie\EventServer\Domain\EntityRepository;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestHandlers\RequestHandler;
use Spatie\EventServer\Server\RequestPayload;

class FindEntityHandler implements RequestHandler
{
    public function __invoke(RequestPayload $payload): Payload
    {
        $entityClass = $payload->get('entityClass');

        $repository = new EntityRepository($entityClass);

        return new Payload([
            'entity' => $repository->find($payload->get('uuid')),
        ]);
    }
}
