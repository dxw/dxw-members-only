<?php

describe(Dxw\MembersOnly\RestAuthenticator::class, function () {
	beforeEach(function () {
		$this->restAuthenticator = new \Dxw\MembersOnly\RestAuthenticator();
	});

	it('implements the registerable interface', function () {
		expect($this->restAuthenticator)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
	});

	describe('->register()', function () {
		it('adds the filter', function () {
			allow('add_filter')->toBeCalled();

			expect('add_filter')->toBeCalled()->once()->with('rest_authentication_errors', [$this->restAuthenticator, 'authenticate'], 10, 1);

			$this->restAuthenticator->register();
		});
	});

	describe('->authenticate()', function () {
		it('allows access if the user is already logged in', function () {
			allow('is_user_logged_in')->toBeCalled()->andReturn(true);

			$actual = $this->restAuthenticator->authenticate(false);
			expect($actual)->toBe(true);
		});
	});
});
