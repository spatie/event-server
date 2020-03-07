<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Container;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;
use Spatie\EventServer\Server\Server;
use Throwable;

class SyncServer extends Server
{
    public function startServer(): void
    {
        // Nothing needs to happen here
    }

    public function handleRequest(RequestPayload $requestPayload): Payload
    {
        Container::$isServer = true;

        try {
            return $requestPayload->resolveHandler()($requestPayload);
        } catch (Throwable $throwable) {
            $this->handleRequestError($throwable);
        } finally {
            Container::$isServer = false;
        }
    }

    public function handleRequestError(Throwable $throwable): void
    {
        throw $throwable;
    }
}
