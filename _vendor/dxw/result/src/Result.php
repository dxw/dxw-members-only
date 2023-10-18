<?php

namespace Dxw\Result;

/** @template T1 */
abstract class Result
{
	/** @return T1 */
	abstract public function unwrap();

	/**
	@param T1 $default
	@return T1
	*/
	abstract public function unwrapOr($default);

	abstract public function isErr(): bool;
	abstract public function getErr(): string;
	abstract public function wrap(string $message): \Dxw\Result\Result;

	/**
	@template T2
	@param T2 $value
	@return self<T2>
	*/
	public static function ok($value): \Dxw\Result\Result
	{
		return new \Dxw\Result\Ok($value);
	}

	public static function err(string $value): \Dxw\Result\Result
	{
		return new \Dxw\Result\Err($value);
	}
}
