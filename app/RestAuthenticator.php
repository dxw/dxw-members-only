<?php

namespace Dxw\MembersOnly;

class RestAuthenticator implements \Dxw\Iguana\Registerable
{
	/** @var string[] */
	private const ENDPOINT_ALLOWLIST = [
		'/wp/v2/posts',
		'/wp/v2/categories',
	];

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
		$loggedIn = is_user_logged_in();
		if ($loggedIn) {
			foreach (self::ENDPOINT_ALLOWLIST as $allowed) {
				if ($this->requestMatchesEndpoint($allowed)) {
					return true;
				}
			}
		}

		return new \WP_Error(
			'rest_forbidden',
			'You must be authenticated to access this REST API endpoint.',
			['status' => 401]
		);
	}

	private function requestMatchesEndpoint(string $endpoint): bool
	{
		$request_uri = '';
		if (isset($_SERVER['REQUEST_URI'])) {
			$request_uri = strtolower($_SERVER['REQUEST_URI']);
		}

		$rest_route = '';
		if (isset($_REQUEST['rest_route']) && is_string($_REQUEST['rest_route'])) {
			$rest_route = strtolower($_REQUEST['rest_route']);
		}

		return str_contains($request_uri, $endpoint) || str_contains($rest_route, $endpoint);
	}
}
