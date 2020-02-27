<?php

use Ramsey\Uuid\Uuid;

if (! function_exists('uuid')) {
    function uuid(): string
    {
        return (string) Uuid::uuid4();
    }
}
