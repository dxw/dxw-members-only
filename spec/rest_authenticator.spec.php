<?php

describe(Dxw\MembersOnly\RestAuthenticator::class, function () {
	beforeEach(function () {
		$this->restAuthenticator = new \Dxw\MembersOnly\RestAuthenticator();
	});

	it('implements the registerable interface', function () {
		expect($this->restAuthenticator)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
	});
});
