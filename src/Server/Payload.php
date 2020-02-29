<?php

namespace Spatie\EventServer\Server;

class Payload
{
    protected array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $text
     *
     * @return static
     */
    public static function unserialize(string $text): self
    {
        return unserialize($text);
    }

    public function serialize(): string
    {
        return serialize($this);
    }

    public function get(string $key)
    {
        // TODO: auto unserialize values
        return $this->data[$key] ?? null;
    }

    // TODO: add `with` which auto serializes values
}
