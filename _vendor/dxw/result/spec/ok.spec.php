<?php

describe(\Dxw\Result\Ok::class, function () {
	describe('->unwrap()', function () {
		it('should allow extracting values', function () {
			$result = new \Dxw\Result\Ok('cat');

			expect($result->unwrap())->toEqual('cat');
		});
	});

	describe('->unwarpOr()', function () {
		it('should allow extracting values', function () {
			$result = new \Dxw\Result\Ok('cat');

			expect($result->unwrapOr('default'))->toEqual('cat');
		});
	});

	describe('->isErr()', function () {
		it('should always be false', function () {
			$result = new \Dxw\Result\Ok('cat');

			expect($result->isErr())->toEqual(false);
		});
	});

	describe('->getErr()', function () {
		it('should raise an exception', function () {
			$result = new \Dxw\Result\Ok('cat');

			expect(function () use ($result) {
				$result->getErr();
			})->toThrow(new \RuntimeException());
		});
	});

	describe('->wrap()', function () {
		it('should raise an exception', function () {
			$result = new \Dxw\Result\Ok('cat');

			expect(function () use ($result) {
				$result->wrap('foobar');
			})->toThrow(new \RuntimeException());
		});
	});
});
