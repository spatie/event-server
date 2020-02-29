<?php

namespace Spatie\EventServer\Server;

use Exception;
use Spatie\EventServer\Container;
use Spatie\EventServer\Server\RequestHandlers\RequestHandler;

class RequestPayload extends Payload
{
    public string $handlerClass;

    public static function make(string $handlerClass, array $data): RequestPayload
    {
        return new self($handlerClass, $data);
    }

    public function __construct(string $handlerClass, array $data)
    {
        $this->handlerClass = $handlerClass;

        parent::__construct($data);
    }

    public function resolveHandler(): RequestHandler
    {
        $handler = Container::make()->resolve($this->handlerClass);

        if (! $handler instanceof RequestHandler) {
            throw new Exception("Could not resolve handler for class {$this->handlerClass}");
        }

        return $handler;
    }
}
