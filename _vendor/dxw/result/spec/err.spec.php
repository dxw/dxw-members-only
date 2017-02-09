<?php

describe(\Dxw\Result\Err::class, function () {
    describe('->unwrap()', function () {
        it('should throw an exception', function () {
            $result = new \Dxw\Result\Err('foo');

            expect(function () use ($result) {
                $result->unwrap();
            })->to->throw(\RuntimeException::class);
        });
    });

    describe('->getErr()', function () {
        it('should report the error given', function () {
            $result = new \Dxw\Result\Err('meow');

            expect($result->getErr())->to->equal('meow');
        });
    });

    describe('->isErr()', function () {
        it('should always return true', function () {
            $result = new \Dxw\Result\Err('bar');

            expect($result->isErr())->to->equal(true);
        });
    });

    describe('->__construct()', function () {
        it('should coerce ints into strings', function () {
            $result = new \Dxw\Result\Err(123);

            expect($result->getErr())->to->equal('123');
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

            expect($newResult)->to->be->instanceof(\Dxw\Result\Err::class);
            expect($newResult->getErr())->to->equal('def: abc');
        });
    });
});
