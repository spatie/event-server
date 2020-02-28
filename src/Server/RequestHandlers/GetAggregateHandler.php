<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use Spatie\EventServer\Domain\AggregateRepository;
use function RingCentral\Psr7\stream_for;

class GetAggregateHandler implements RequestHandler
{
    private AggregateRepository $repository;

    public function __construct(AggregateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();

        $aggregate = $this->repository->resolve($body['aggregateClass'], $body['aggregateUuid']);

        return new Response(200, [], stream_for(json_encode([
            'aggregate' => serialize($aggregate),
        ])));
    }
}
