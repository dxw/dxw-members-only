<?php

use Kahlan\Plugin\Quit;
use Kahlan\QuitException;

describe(Dxw\MembersOnly\Redirect::class, function () {
	beforeEach(function () {
		$this->redirect = new \Dxw\MembersOnly\Redirect();
	});

	it('implements the registerable interface', function () {
		expect($this->redirect)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
	});

	describe('->register()', function () {
		it('adds the action', function () {
			allow('add_action')->toBeCalled();

			expect('add_action')->toBeCalled()->once()->with('init', [$this->redirect, 'redirect_request'], 10, 0);

			$this->redirect->register();
		});
	});

	describe('->redirect()', function () {
		context('$root param is true', function () {
			it('redirects to the root redirect option then exits', function () {
				global $_SERVER;
				$_SERVER = [];
				allow('get_option')->toBeCalled()->with('dxw_members_only_redirect_root')->andReturn('http://localhost/root-redirect');
				allow('header')->toBeCalled();

				expect('header')->toBeCalled()->once()->with('HTTP/1.1 303 See Other');
				expect('header')->toBeCalled()->once()->with('x-redirect-by: dxw-members-only');
				expect('header')->toBeCalled()->once()->with('Location: http://localhost/root-redirect');

				Quit::disable();
				$closure = function () {
					$this->redirect->redirect(true);
				};
				expect($closure)->toThrow(new QuitException());
			});
		});
		context('$root param is false', function () {
			it('redirects to the default redirect option then exits', function () {
				global $_SERVER;
				$_SERVER = [];
				allow('get_option')->toBeCalled()->with('dxw_members_only_redirect')->andReturn('http://localhost/default-redirect');
				allow('header')->toBeCalled();

				expect('header')->toBeCalled()->once()->with('HTTP/1.1 303 See Other');
				expect('header')->toBeCalled()->once()->with('x-redirect-by: dxw-members-only');
				expect('header')->toBeCalled()->once()->with('Location: http://localhost/default-redirect');

				Quit::disable();
				$closure = function () {
					$this->redirect->redirect(false);
				};
				expect($closure)->toThrow(new QuitException());
			});
		});
		context('a REQUEST_URI is set', function () {
			context('but the redirect option does not contain a %return_path%', function () {
				it('redirects to the appropriate redirect option, without replacement', function () {
					global $_SERVER;
					$_SERVER = [
						'REQUEST_URI' => 'http://localhost/foobar'
					];
					allow('get_option')->toBeCalled()->with('dxw_members_only_redirect')->andReturn('http://localhost/default-redirect');
					allow('header')->toBeCalled();

					expect('header')->toBeCalled()->once()->with('HTTP/1.1 303 See Other');
					expect('header')->toBeCalled()->once()->with('x-redirect-by: dxw-members-only');
					expect('header')->toBeCalled()->once()->with('Location: http://localhost/default-redirect');

					Quit::disable();
					$closure = function () {
						$this->redirect->redirect(false);
					};
					expect($closure)->toThrow(new QuitException());
				});
			});
			context('and the redirect option does contain a %return_path%', function () {
				it('redirects to the appropriate redirect option, and replaces %return_path% with the originally requested URL', function () {
					global $_SERVER;
					$_SERVER = [
						'REQUEST_URI' => 'http://localhost/foobar'
					];
					allow('get_option')->toBeCalled()->with('dxw_members_only_redirect')->andReturn('http://localhost/default-redirect?redirect=%return_path%');
					allow('header')->toBeCalled();

					expect('header')->toBeCalled()->once()->with('HTTP/1.1 303 See Other');
					expect('header')->toBeCalled()->once()->with('x-redirect-by: dxw-members-only');
					expect('header')->toBeCalled()->once()->with('Location: http://localhost/default-redirect?redirect=' . urlencode('http://localhost/foobar'));

					Quit::disable();
					$closure = function () {
						$this->redirect->redirect(false);
					};
					expect($closure)->toThrow(new QuitException());
				});
			});
		});
	});

	describe('->serve_uploads()', function () {
		context('no request uri is set', function () {
			it('does nothing', function () {
				global $_SERVER;
				$_SERVER = [];

				expect('dmo_strip_query')->not->toBeCalled();

				$this->redirect->serve_uploads();
			});
		});
		context('a request uri is set, but it is not for /wp-content/uploads/', function () {
			it('does nothing', function () {
				global $_SERVER;
				$_SERVER = [
					'REQUEST_URI' => 'http://localhost/foobar.php'
				];
				allow('dmo_strip_query')->toBeCalled()->andRun(function ($input) {
					return $input;
				});

				expect('wp_upload_dir')->not->toBeCalled();

				$this->redirect->serve_uploads();
			});
		});
		context('a request uri is set, and it is for /wp-content/uploads/ or /wp-content/blogs.dir/', function () {
			beforeEach(function () {
				global $_SERVER;
				$_SERVER = [
					'REQUEST_URI' => 'http://localhost/wp-content/uploads/foobar.txt'
				];
				allow('dmo_strip_query')->toBeCalled()->andRun(function ($input) {
					return $input;
				});
				allow('wp_upload_dir')->toBeCalled()->andReturn([
					'baseurl' => 'http://localhost/wp-content/uploads',
					'basedir' => '/path/to/wordpress/wp-content/uploads'
				]);
				allow('realpath')->toBeCalled()->andRun(function ($input) {
					return $input;
				});
			});
			context('but is not requesting a regular file', function () {
				it('does nothing', function () {
					allow('is_file')->toBeCalled()->andReturn(false);

					expect('header')->not->toBeCalled();

					$this->redirect->serve_uploads();
				});
			});
			context('but the file request is not readable', function () {
				it('does nothing', function () {
					allow('is_file')->toBeCalled()->andReturn(true);
					allow('is_readable')->toBeCalled()->andReturn(false);

					expect('header')->not->toBeCalled();

					$this->redirect->serve_uploads();
				});
			});
			context('but the file path does not match the upload dir', function () {
				it('does nothing', function () {
					allow('realpath')->toBeCalled()->andReturn(
						'/path/one',
						'/path/two'
					);
					allow('is_file')->toBeCalled()->andReturn(true);
					allow('is_readable')->toBeCalled()->andReturn(false);

					expect('header')->not->toBeCalled();

					$this->redirect->serve_uploads();
				});
			});
			context('all file path info is correct', function () {
				beforeEach(function () {
					allow('realpath')->toBeCalled()->andReturn(
						'/path/to/wp-content/uploads/the/file.txt',
						'/path/to/wp-content/uploads'
					);
					allow('is_file')->toBeCalled()->andReturn(true);
					allow('is_readable')->toBeCalled()->andReturn(true);
				});
				context('an HTTP_IF_MODIFIED_SINCE header is sent, and the file has not been modified since it was last requested', function () {
					it('returns a 304 response, and last-modified header of the ims timestamp', function () {
						global $_SERVER;
						$_SERVER['HTTP_IF_MODIFIED_SINCE'] = gmdate('D, d M Y H:i:s T', 1751637600);
						allow('filemtime')->toBeCalled()->andReturn(1751637600);
						allow('http_response_code')->toBeCalled();
						allow('header')->toBeCalled();
						allow($this->redirect)->toReceive('_exit');

						expect('http_response_code')->toBeCalled()->once()->with(304);
						expect('header')->toBeCalled()->once()->with('Last-Modified: ' . gmdate('D, d M Y H:i:s T', 1751637600));

						Quit::disable();
						$closure = function () {
							$this->redirect->serve_uploads();
						};
						expect($closure)->toThrow(new QuitException());
					});
				});
				context('an HTTP_IF_MODIFIED_SINCE header is sent, and the file has been modified since it was last requested', function () {
					it('returns a header with a max age of the static max age', function () {
						global $_SERVER;
						$_SERVER['HTTP_IF_MODIFIED_SINCE'] = gmdate('D, d M Y H:i:s T', 0);
						allow('filemtime')->toBeCalled()->andReturn(1751637600);
						allow('wp_check_filetype')->toBeCalled()->andReturn([
							'type' => 'txt'
						]);
						allow('header')->toBeCalled();
						allow('absint')->toBeCalled()->andRun(function ($input) {
							return $input;
						});
						allow('get_option')->toBeCalled()->andReturn(30);
						allow('filesize')->toBeCalled()->andReturn(30000);
						allow('readfile')->toBeCalled();
						allow('ob_get_flush')->toBeCalled();

						allow($this->redirect)->toReceive('_exit');

						expect('http_response_code')->not->toBeCalled();
						expect('header')->toBeCalled()->with('Cache-Control: private, max-age=30');

						Quit::disable();
						$closure = function () {
							$this->redirect->serve_uploads();
						};
						expect($closure)->toThrow(new QuitException());
					});
					it('returns a header with a max age of the static max age if that is stored as a string', function () {
						global $_SERVER;
						$_SERVER['HTTP_IF_MODIFIED_SINCE'] = gmdate('D, d M Y H:i:s T', 0);
						allow('filemtime')->toBeCalled()->andReturn(1751637600);
						allow('wp_check_filetype')->toBeCalled()->andReturn([
							'type' => 'txt'
						]);
						allow('header')->toBeCalled();
						allow('absint')->toBeCalled()->andRun(function ($input) {
							return $input;
						});
						allow('get_option')->toBeCalled()->andReturn('30');
						allow('filesize')->toBeCalled()->andReturn(30000);
						allow('readfile')->toBeCalled();
						allow('ob_get_flush')->toBeCalled();

						allow($this->redirect)->toReceive('_exit');

						expect('http_response_code')->not->toBeCalled();
						expect('header')->toBeCalled()->with('Cache-Control: private, max-age=30');

						Quit::disable();
						$closure = function () {
							$this->redirect->serve_uploads();
						};
						expect($closure)->toThrow(new QuitException());
					});
					it('returns a header with a max age of the option of 0 if the static max age option does not exist', function () {
						global $_SERVER;
						$_SERVER['HTTP_IF_MODIFIED_SINCE'] = gmdate('D, d M Y H:i:s T', 0);
						allow('filemtime')->toBeCalled()->andReturn(1751637600);
						allow('wp_check_filetype')->toBeCalled()->andReturn([
							'type' => 'txt'
						]);
						allow('header')->toBeCalled();
						allow('absint')->toBeCalled()->andRun(function ($input) {
							return $input;
						});
						allow('get_option')->toBeCalled()->andReturn(false);
						allow('filesize')->toBeCalled()->andReturn(30000);
						allow('readfile')->toBeCalled();
						allow('ob_get_flush')->toBeCalled();

						allow($this->redirect)->toReceive('_exit');

						expect('http_response_code')->not->toBeCalled();
						expect('header')->toBeCalled()->with('Cache-Control: private, max-age=0');

						Quit::disable();
						$closure = function () {
							$this->redirect->serve_uploads();
						};
						expect($closure)->toThrow(new QuitException());
					});
				});
			});
		});
	});

	describe('current_ip_in_whitelist()', function () {
		context('no REMOTE_ADDR is set', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [];
				allow('get_option')->toBeCalled()->andReturn('0.0.0.0/0');
				expect($this->redirect->current_ip_in_whitelist())->toEqual(false);
			});
		});
		context('no IP allow list is set', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'REMOTE_ADDR' => '127.0.0.1'
				];
				allow('get_option')->toBeCalled()->andReturn(false);
				expect($this->redirect->current_ip_in_whitelist())->toEqual(false);
			});
		});
		context('current IP is not within allow list', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'REMOTE_ADDR' => '127.0.0.1'
				];
				allow('get_option')->toBeCalled()->andReturn('124.0.0.1');
				expect($this->redirect->current_ip_in_whitelist())->toEqual(false);
			});
		});
		context('IP check returns an error', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'REMOTE_ADDR' => '127.0.0.1'
				];
				allow('get_option')->toBeCalled()->andReturn('0.0.0.0/0');
				allow('Dxw\CIDR\IP')->toReceive('::contains')->andReturn(new Dxw\Result\Err('foobar'));
				expect($this->redirect->current_ip_in_whitelist())->toEqual(false);
			});
		});
		context('current IP is within allow list range', function () {
			it('returns true', function () {
				global $_SERVER;
				$_SERVER = [
					'REMOTE_ADDR' => '127.0.0.1'
				];
				allow('get_option')->toBeCalled()->andReturn('0.0.0.0/0');

				expect($this->redirect->current_ip_in_whitelist())->toEqual(true);
			});
		});
		context('current IP is an IPv4-mapped IPv6 address', function () {
			it('returns true', function () {
				global $_SERVER;
				$_SERVER = [
					'REMOTE_ADDR' => '::ffff:127.0.0.1'
				];
				allow('get_option')->toBeCalled()->andReturn('0.0.0.0/0');

				expect($this->redirect->current_ip_in_whitelist())->toEqual(true);
			});
		});
	});

	describe('->referrer_in_allow_list()', function () {
		context('no HTTP_REFERER is set', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [];
				allow('get_option')->toBeCalled()->andReturn('/referrer');

				expect($this->redirect->referrer_in_allow_list())->toEqual(false);
			});
		});
		context('no referrer allow list is set', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'HTTP_REFERER' => '/referrer'
				];
				allow('get_option')->toBeCalled()->andReturn(false);

				expect($this->redirect->referrer_in_allow_list())->toEqual(false);
			});
		});
		context('no referrer allow list is set', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'HTTP_REFERER' => '/referrer'
				];
				allow('get_option')->toBeCalled()->andReturn(false);

				expect($this->redirect->referrer_in_allow_list())->toEqual(false);
			});
		});
		context('the referrer is an external URL ending in the same string', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'HTTP_REFERER' => 'http://external.com/referrer'
				];
				allow('get_option')->toBeCalled()->andReturn('/referrer');
				allow('get_site_url')->toBeCalled()->andReturn('http://localhost');

				expect($this->redirect->referrer_in_allow_list())->toEqual(false);
			});
		});
		context('the referrer is an external URL with the local referrer URL embedded as a query string', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'HTTP_REFERER' => 'http://external.com?test=http://localhost/referrer'
				];
				allow('get_option')->toBeCalled()->andReturn('/referrer');
				allow('get_site_url')->toBeCalled()->andReturn('http://localhost');

				expect($this->redirect->referrer_in_allow_list())->toEqual(false);
			});
		});
		context('the referrer is on the host URL but does not match the allow list', function () {
			it('returns false', function () {
				global $_SERVER;
				$_SERVER = [
					'HTTP_REFERER' => 'http://localhost/foobar'
				];
				allow('get_option')->toBeCalled()->andReturn('/referrer');
				allow('get_site_url')->toBeCalled()->andReturn('http://localhost');

				expect($this->redirect->referrer_in_allow_list())->toEqual(false);
			});
		});
		context('the referrer is on the host URL and does match the allow list', function () {
			it('returns true', function () {
				global $_SERVER;
				$_SERVER = [
					'HTTP_REFERER' => 'http://localhost/referrer'
				];
				allow('get_option')->toBeCalled()->andReturn('/referrer');
				allow('get_site_url')->toBeCalled()->andReturn('http://localhost');

				expect($this->redirect->referrer_in_allow_list())->toEqual(true);
			});
		});
	});
});
