<?php

use Kahlan\Plugin\Double;

describe(Dxw\MembersOnly\RestAuthenticator::class, function () {
	beforeEach(function () {
		$this->restAuthenticator = new \Dxw\MembersOnly\RestAuthenticator();
	});

	it('implements the registerable interface', function () {
		expect($this->restAuthenticator)->toBeAnInstanceOf(\dxw\iguana\registerable::class);
	});

	describe('->register()', function () {
		it('adds the filter', function () {
			allow('add_filter')->toBeCalled();

			expect('add_filter')->toBeCalled()->once()->with('rest_authentication_errors', [$this->restAuthenticator, 'authenticate'], 10, 1);

			$this->restAuthenticator->register();
		});
	});

	describe('->authenticate()', function () {
		beforeEach(function () {
			$_SERVER['REQUEST_URI'] = '';
			$_REQUEST['rest_route'] = '';

			$allowed_endpoints = "/wp/v2/posts\n";
			allow('get_option')->toBeCalled()->andReturn($allowed_endpoints);

			$this->wpError = Double::instance([
				'class' => '\WP_Error',
			]);
		});

		it('allows access for logged-in users on whitelisted endpoints', function () {
			allow('is_user_logged_in')->toBeCalled()->andReturn(true);

			$_SERVER['REQUEST_URI'] = '/wp/v2/posts';

			$actual = $this->restAuthenticator->authenticate(null);
			expect($actual)->toBe(true);
		});

		it('blocks logged-in users for non-whitelisted endpoints', function () {
			allow('is_user_logged_in')->toBeCalled()->andReturn(true);

			$_SERVER['REQUEST_URI'] = '/wp/v2/non-whitelisted';

			$actual = $this->restAuthenticator->authenticate(null);
			expect($actual)->toBeAnInstanceOf($this->wpError);
		});

		it('blocks request if not authenticated', function () {
			allow('is_user_logged_in')->toBeCalled()->andReturn(false);

			$_SERVER['REQUEST_URI'] = '/wp/v2/posts';

			$actual = $this->restAuthenticator->authenticate(null);
			expect($actual)->toBeAnInstanceOf($this->wpError);
		});
	});
});
