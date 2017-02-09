<?php

namespace Dxw\Result;

class Ok extends Result
{
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function unwrap()
    {
        return $this->value;
    }

    public function isErr(): bool
    {
        return false;
    }

    public function getErr(): string
    {
        throw new \RuntimeException("This is not an error value");
    }
}
