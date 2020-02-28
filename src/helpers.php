<?php

use Ramsey\Uuid\Uuid;

if (! function_exists('uuid')) {
    function uuid(): string
    {
        return (string) Uuid::uuid4();
    }
}

if (! function_exists('event')) {
    function event(object $event)
    {

    }
}
