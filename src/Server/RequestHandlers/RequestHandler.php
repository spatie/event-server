<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestHandler
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface;
}
