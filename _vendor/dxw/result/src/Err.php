<?php

namespace Dxw\Result;

class Err extends Result
{
	/** @var string */
	private $message;

	public function __construct(string $message)
	{
		$this->message = $message;
	}

	public function unwrap()
	{
		throw new \RuntimeException("Can't unwrap error");
	}

	/**
	@param mixed $default
	@psalm-return mixed
	*/
	public function unwrapOr($default)
	{
		return $default;
	}

	public function getErr(): string
	{
		return $this->message;
	}

	public function isErr(): bool
	{
		return true;
	}

	public function wrap(string $message): \Dxw\Result\Result
	{
		return new self(sprintf('%s: %s', $message, $this->getErr()));
	}
}
