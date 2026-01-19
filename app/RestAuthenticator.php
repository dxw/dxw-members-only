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
		if (is_user_logged_in()) {
			return true;
		}

		return new \WP_Error(
			'rest_forbidden',
			'You must be authenticated to access the REST API.',
			[ 'status' => 401 ]
		);
	}
}
