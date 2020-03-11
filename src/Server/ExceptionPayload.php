<?php

namespace Spatie\EventServer\Server;

use Exception;

class ExceptionPayload extends Payload
{
    public function __construct(Exception $exception)
    {
        $this->data = [
            'exceptionClass' => get_class($exception),
            'message' => $exception->getMessage(),
        ];
    }

    public function toException(): Exception
    {
        $exceptionClass = $this->data['exceptionClass'];
        $message = $this->data['message'];

        return new $exceptionClass($message);
    }
}
