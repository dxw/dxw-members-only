<?php

describe(\Dxw\Result\Result::class, function () {
    describe('->ok()', function () {
        it('should return an Ok value', function () {
            $result = \Dxw\Result\Result::ok('xyz');

            expect($result)->to->be->instanceof(\Dxw\Result\Ok::class);
            expect($result->unwrap())->to->equal('xyz');
        });
    });

    describe('->err()', function () {
        it('should return an Err value', function () {
            $result = \Dxw\Result\Result::err('xyz');

            expect($result)->to->be->instanceof(\Dxw\Result\Err::class);
            expect($result->getErr())->to->equal('xyz');
        });
    });
});
