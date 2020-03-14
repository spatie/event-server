<?php

namespace App;

use Spatie\EventServer\Config as BaseConfig;

class Config extends BaseConfig
{
    public function databaseConnection(): array
    {
        return [
            'dbname' => 'event_server',
            'user' => 'root',
            'password' => 'root',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];
    }
}
