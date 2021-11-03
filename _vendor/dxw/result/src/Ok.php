<?php

namespace Dxw\Result;

/** @template T */

class Ok extends Result
{
    /** @var T */
    private $value;

    /** @param T $value */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /** @return T */
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

    public function wrap(string $message): \Dxw\Result\Result
    {
        throw new \RuntimeException("This is not an error value");
    }
}
