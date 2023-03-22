<?php

/** @noinspection ReturnTypeCanBeDeclaredInspection */

namespace GhostZero\Tmi;

use ArrayAccess;

class Tags implements ArrayAccess
{
    private array $tags;

    public function __construct(array $tags = [])
    {
        $this->tags = $tags;
    }

    public static function parse(string $message): self
    {
        $result = [];
        $rawTags = explode(';', $message);
        foreach ($rawTags as $rawTag) {
            $data = explode('=', $rawTag);
            $result[$data[0]] = $data[1] ?? null;
        }

        return new self($result);
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->tags[] = $value;
        } else {
            $this->tags[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->tags[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->tags[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->tags[$offset] ?? null;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
