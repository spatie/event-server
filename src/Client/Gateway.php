<?php

namespace Spatie\EventServer\Client;

use Spatie\EventServer\Server\Server;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Gateway
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function event(object $event): void
    {
        if (! isset($event->uuid)) {
            $event->uuid = uuid();
        }

        $this->client->request(
            'POST',
            $this->uri('events'),
            [
                'body' => [
                    'event' => serialize($event),
                ],
            ]
        );
    }

    protected function uri(string $path): string
    {
        return 'http://' . rtrim(Server::URL, '/') . '/' . ltrim($path, '/');
    }
}
