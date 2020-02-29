<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;

interface RequestHandler
{
    public function __invoke(RequestPayload $payload): Payload;
}
