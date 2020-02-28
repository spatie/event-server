<?php

namespace Spatie\EventServer\Client;

use Psr\Http\Message\ResponseInterface;
use React\Http\Response;
use Spatie\EventServer\Domain\Aggregate;
use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Server\Server;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Gateway
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function event(Event $event): void
    {
        if (! isset($event->uuid)) {
            $event->uuid = uuid();
        }

        $this->request('POST', $this->uri('events'), [
            'event' => serialize($event),
        ]);
    }

    public function getAggregate(string $aggregateClass, string $aggregateUuid): Aggregate
    {
        $response = $this->request('GET', $this->uri('aggregate'), [
            'aggregateClass' => $aggregateClass,
            'aggregateUuid' => $aggregateUuid,
        ]);

        $response = json_decode((string) $response->getBody(), true);

        return unserialize($response['aggregate']);
    }

    protected function request(string $verb, string $uri, array $body): ResponseInterface
    {
        $symfonyResponse = $this->client->request($verb, $uri, ['body' => $body]);

        return (new Response(
            $symfonyResponse->getStatusCode(),
            $symfonyResponse->getHeaders(),
            $symfonyResponse->getContent(false)
        ));
    }

    protected function uri(string $path): string
    {
        return 'http://' . rtrim(Server::URL, '/') . '/' . ltrim($path, '/');
    }
}
