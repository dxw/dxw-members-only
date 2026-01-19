<?php

namespace Dxw\MembersOnly;

class RestAuthenticator implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_filter('rest_authentication_errors', [$this, 'authenticate'], 10, 1);
	}

	/**
	 * @param \WP_Error|null|true $errors
	 * @return \WP_Error|null|true
	 */
	public function authenticate($errors)
	{
		return null;
	}
}
