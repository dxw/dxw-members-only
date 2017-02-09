<?php

describe(\Dxw\Result\Ok::class, function () {
    describe('->unwrap()', function () {
        it('should allow extracting values', function () {
            $result = new \Dxw\Result\Ok('cat');

            expect($result->unwrap())->to->equal('cat');
        });
    });

    describe('->isErr()', function () {
        it('should always be false', function () {
            $result = new \Dxw\Result\Ok('cat');

            expect($result->isErr())->to->equal(false);
        });
    });

    describe('->getErr()', function () {
        it('should raise an exception', function () {
            $result = new \Dxw\Result\Ok('cat');

            expect(function () use ($result) {
                $result->getErr();
            })->to->throw(\RuntimeException::class);
        });
    });
});
