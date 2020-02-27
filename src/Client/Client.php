<?php

namespace Spatie\EventServer\Client;

use Spatie\EventServer\Server\Server;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    public function event(object $event): void
    {
        if (! isset($event->uuid)) {
            $event->uuid = uuid();
        }

        $this->client->request(
            'POST',
            $this->url('events'),
            [
                'body' => [
                    'event' => serialize($event),
                ],
            ]
        );
    }

    private function url(string $path): string
    {
        return 'http://' . Server::URL . '/' . $path;
    }
}
