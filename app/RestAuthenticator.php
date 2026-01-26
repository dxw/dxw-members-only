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
		if (!is_user_logged_in()) {
			return new \WP_Error(
				'rest_not_logged_in',
				'You must be authenticated to access this endpoint.',
				['status' => 401]
			);
		}

		$endpoint_allow_list = explode("\n", (string) get_option('dxw_members_only_endpoint_allow_list'));
		foreach ($endpoint_allow_list as $endpoint) {
			$endpoint = trim($endpoint);

			if (!empty($endpoint) && $this->requestMatchesEndpoint($endpoint)) {
				return true;
			}
		}

		return new \WP_Error(
			'rest_forbidden',
			'You do not have permission to access this endpoint.',
			['status' => 403]
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
