<?php

describe(\Dxw\Result\Err::class, function () {
	describe('->unwrap()', function () {
		it('should throw an exception', function () {
			$result = new \Dxw\Result\Err('foo');

			expect(function () use ($result) {
				$result->unwrap();
			})->toThrow(new \RuntimeException());
		});
	});

	describe('->unwrapOr()', function () {
		it('should return the value given', function () {
			$result = new \Dxw\Result\Err('foo');

			expect($result->unwrapOr('default'))->toEqual('default');
		});
	});

	describe('->getErr()', function () {
		it('should report the error given', function () {
			$result = new \Dxw\Result\Err('meow');

			expect($result->getErr())->toEqual('meow');
		});
	});

	describe('->isErr()', function () {
		it('should always return true', function () {
			$result = new \Dxw\Result\Err('bar');

			expect($result->isErr())->toEqual(true);
		});
	});

	describe('->__construct()', function () {
		it('should coerce ints into strings', function () {
			$result = new \Dxw\Result\Err(123);

			expect($result->getErr())->toEqual('123');
		});

		//TODO: how do we do this?
		// it('should reject arrays', function () {
		//     expect(function () {
		//         new \Dxw\Result\Err(['abc', 'def']);
		//     })->to->throw(\TypeError::class);
		// });
	});

	describe('->wrap()', function () {
		it('should concatenate error strings', function () {
			$result = new \Dxw\Result\Err('abc');
			$newResult = $result->wrap('def');

			expect($newResult)->toBeAnInstanceOf(\Dxw\Result\Err::class);
			expect($newResult->getErr())->toEqual('def: abc');
		});
	});
});
