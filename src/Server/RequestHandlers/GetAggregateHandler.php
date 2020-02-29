<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Spatie\EventServer\Domain\AggregateRepository;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;

class GetAggregateHandler implements RequestHandler
{
    private AggregateRepository $repository;

    public function __construct(AggregateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RequestPayload $payload): Payload
    {
        $aggregate = $this->repository->resolve(
            $payload->get('aggregateClass'),
            $payload->get('aggregateUuid')
        );

        return new Payload([
            'aggregate' => serialize($aggregate),
        ]);
    }
}
