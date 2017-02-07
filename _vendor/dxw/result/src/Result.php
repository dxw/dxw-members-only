<?php

namespace Dxw\Result;

abstract class Result
{
    abstract public function unwrap();
    abstract public function isErr(): bool;
    abstract public function getErr(): string;

    public static function ok($value): \Dxw\Result\Result
    {
        return new \Dxw\Result\Ok($value);
    }

    public static function err($value): \Dxw\Result\Result
    {
        return new \Dxw\Result\Err($value);
    }
}
