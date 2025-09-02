<?php

namespace Dxw\MembersOnly;

class Redirect implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_action('init', [$this, 'handle_request'], -99999999999, 0);
	}

	public function handle_request(): void
	{
		// Fix for wp-cli
		if (defined('WP_CLI_ROOT')) {
			return;
		}

		/** @var int @max_age */
		$max_age = absint((int) get_option('dxw_members_only_max_age'));
		/** @var int @max_age_public */
		$max_age_public = absint((int) get_option('dxw_members_only_max_age_public'));

		do_action('dxw_members_only');
		if (
			defined('dxw_members_ONLY_PASSTHROUGH') ||
			is_user_logged_in() ||
			apply_filters('dxw_members_only_redirect', false) === true
		) {
			header('Cache-Control: private, max-age=' . $max_age);
			$this->serve_uploads();
			return;
		}

		// Get path component
		/**
		 * @psalm-suppress UndefinedFunction
		 * @var string $path
		 */
		$path = dmo_strip_query(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');

		// Always allow /wp-login.php
		if (\Missing\Strings::endsWith($path, 'wp-login.php')) {
			return;
		}

		// Always allow POST /wp-admin/admin-ajax.php with action=heartbeat
		if (\Missing\Strings::endsWith($path, 'wp-admin/admin-ajax.php') && isset($_POST['action']) && $_POST['action'] === 'heartbeat') {
			return;
		}

		// IP & referrer allow lists
		if ($this->current_ip_in_whitelist() || $this->referrer_in_allow_list()) {
			header('Cache-Control: private, max-age=' . $max_age);
			$this->serve_uploads();
			return;
		}

		// List
		$hit = false;
		$list = explode("\n", (string) get_option('dxw_members_only_list_content'));

		foreach ($list as $w) {
			$w = trim($w);

			if (empty($w)) {
				continue;
			}

			# /welcome => /welcome, /welcome/
			if ($path === $w || $path === $w . '/') {
				$hit = true;
				break;
			}

			# /welcome/ => /welcome
			if (\Missing\Strings::endsWith($w, '/') && $path === substr($w, 0, -1)) {
				$hit = true;
				break;
			}

			# /welcome/* => /welcome
			if (\Missing\Strings::endsWith($w, '/*') && $path === substr($w, 0, -2)) {
				$hit = true;
				break;
			}

			# /welcome/* => /welcome/.*
			if (\Missing\Strings::endsWith($w, '*') && \Missing\Strings::startsWith($path, substr($w, 0, -1))) {
				$hit = true;
				break;
			}
		}

		if ($hit) {
			header('Cache-Control: public, max-age=' . $max_age_public);
			$this->serve_uploads();
			return;
		}

		header('Cache-Control: private, max-age=' . $max_age);
		$this->redirect($path === '/');
	}

	/**
	 * Handle redirect for users when not logged in or viewing whitelisted content
	 *
	 * @param  boolean $root Whether attempting to view root or not
	 * @return never
	 */
	public function redirect($root)
	{
		// Redirect
		if ($root) {
			$redirect = (string) get_option('dxw_members_only_redirect_root');
		} else {
			$redirect = (string) get_option('dxw_members_only_redirect');
		}

		// %return_path%
		if (isset($_SERVER['REQUEST_URI'])) {
			$redirect = str_replace('%return_path%', urlencode($_SERVER['REQUEST_URI']), $redirect);
		}

		header('HTTP/1.1 303 See Other');
		header('x-redirect-by: dxw-members-only');
		header('Location: '.$redirect);
		$this->_exit();
	}

	/**
	 * Handle request for uploaded content
	 * @return void
	 */
	public function serve_uploads()
	{
		/** @var string $req
		 * @psalm-suppress UndefinedFunction
		 */
		$req = isset($_SERVER['REQUEST_URI']) ? dmo_strip_query($_SERVER['REQUEST_URI']) : '';
		if (
			str_contains($req, '/wp-content/uploads/') || str_contains($req, '/wp-content/blogs.dir/')
		) {
			$upload_dir = wp_upload_dir();
			$baseurl = preg_replace('%^https?://[^/]+(/.*)$%', '$1', $upload_dir['baseurl']);
			$basedir = $upload_dir['basedir'];
			$file = preg_replace("[^{$baseurl}]", $basedir, $req);
			$realFilePath = realpath($file);
			$realUploadDir = realpath($basedir);

			if (is_file($file) && is_readable($file) && \Missing\Strings::startsWith($realFilePath, $realUploadDir.'/')) {
				$ims_timestamp = gmdate('D, d M Y H:i:s T', filemtime($file));

				if (array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER) && $ims_timestamp === $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
					## we don't set Etag so `If-None-Match:` doesn't need checking

					http_response_code(304);
					header('Last-Modified: ' . $ims_timestamp);
				} else {
					$mime = wp_check_filetype($file);
					$type = 'application/octet-stream';
					if ($mime['type'] !== false) {
						$type = $mime['type'];
					}

					header('Accept-Ranges: none');
					header('Content-Type: ' . $type);
					header('Content-Length: ' . filesize($file));
					header('Last-Modified: ' . $ims_timestamp);

					header('X-Accel-Buffering: no');
					/** @var mixed $max_age_option_value */
					$max_age_option_value = get_option('dxw_members_only_max_age_static');
					/** @var int $max_age_static */
					$max_age_static = absint((is_int($max_age_option_value) || is_string($max_age_option_value)) ? $max_age_option_value : 0);
					header('Cache-Control: private, max-age=' . $max_age_static);
					ob_get_flush();
					readfile($file);
				}
				$this->_exit();
			}
		}
	}

	private function ip_in_range(string $ip, string $range): bool
	{
		$range = trim($range);

		# Handle IPv4-mapped IPv6 addresses
		if (preg_match('_^::ffff:(.*)$_', $ip, $m)) {
			$ip = $m[1];
		}

		$result = \Dxw\CIDR\IP::contains($range, $ip);
		if ($result->isErr()) {
			return false;
		}

		/** @var bool */
		return $result->unwrap();
	}

	public function current_ip_in_whitelist(): bool
	{
		$ip_list = explode("\n", (string) get_option('dxw_members_only_ip_whitelist'));
		foreach ($ip_list as $ip) {
			$ip = trim($ip);
			if (!empty($ip) && isset($_SERVER['REMOTE_ADDR']) && $this->ip_in_range($_SERVER['REMOTE_ADDR'], $ip)) {
				return true;
			}
		}
		return false;
	}

	public function referrer_in_allow_list(): bool
	{
		$referrer_list = explode("\n", (string) get_option('dxw_members_only_referrer_allow_list'));
		/*
		* If there is no referrer header, or if we have no configured referrers to
		* whitelist we can stop here.
		*/
		if (isset($_SERVER['HTTP_REFERER'])) {
			foreach ($referrer_list as $referrer) {
				if (!empty($referrer)) {
					/*
					* Add the site url to the referrer string to ensure that external
					* referrers can't be used here.
					*/
					$whitelisted_referrer = get_site_url() . $referrer;
					$referrer_check = strpos($_SERVER['HTTP_REFERER'], $whitelisted_referrer);
					/*
					* Check that there is a match, and that match is at the start of the referrer string.
					* This is to ensure that the referrer being whitelisted can't be fooled by having
					* a whitelisted referrer passed in as a parameter on the referrer string.
					*/
					if ($referrer_check !== false && $referrer_check == 0) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public function _exit(): never
	{
		exit();
	}
}
