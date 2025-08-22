<?php

namespace Dxw\MembersOnly;

class Redirect implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_action('init', [$this, 'redirect_request'], 10, 0);
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

	public function _exit(): never
	{
		exit();
	}
}
