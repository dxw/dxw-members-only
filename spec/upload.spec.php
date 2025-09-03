<?php

describe(\Dxw\MembersOnly\Upload::class, function () {
	beforeEach(function () {
		if (!defined('ABSPATH')) {
			define('ABSPATH', '');
		}
		$this->upload = new \Dxw\MembersOnly\Upload();
	});

	it('implements the Registerable interface', function () {
		expect($this->upload)->toBeAnInstanceOf(\Dxw\Iguana\Registerable::class);
	});

	describe('->add_fields()', function () {
		context('no SCRIPT_FILENAME is set', function () {
			it('returns the fields unchanged', function () {
				global $_SERVER;
				$_SERVER = [];

				$fields = [
					'foo' => [
						'bar'
					]
				];
				$post = \Kahlan\Plugin\Double::instance([
					'class' => '\WP_Post'
				]);

				expect($this->upload->add_fields($fields, $post))->toEqual([
					'foo' => [
						'bar'
					]
				]);
			});
		});
		context('SCRIPT_FILENAME is set, but not to async-upload.php', function () {
			it('returns the fields unchanged', function () {
				global $_SERVER;
				$_SERVER = [
					'SCRIPT_FILENAME' => ABSPATH . 'wp-admin/edit.php'
				];

				$fields = [
					'foo' => [
						'bar'
					]
				];
				$post = \Kahlan\Plugin\Double::instance([
					'class' => '\WP_Post'
				]);

				expect($this->upload->add_fields($fields, $post))->toEqual([
					'foo' => [
						'bar'
					]
				]);
			});
		});
		context('SCRIPT_FILENAME is set to async-upload.php', function () {
			it('returns the fields with dmo_add_to_list added', function () {
				global $_SERVER;
				$_SERVER = [
					'SCRIPT_FILENAME' => ABSPATH . 'wp-admin/async-upload.php'
				];
				$fields = [
					'foo' => [
						'bar'
					]
				];
				$post = \Kahlan\Plugin\Double::instance([
					'class' => '\WP_Post'
				]);
				$post->ID = 123;
				allow('realpath')->toBeCalled()->andReturn(ABSPATH . 'wp-admin/async-upload.php');
				allow('get_option')->toBeCalled()->andReturn(false);
				allow('__')->toBeCalled()->andRun(function ($input) {
					return $input;
				});

				expect($this->upload->add_fields($fields, $post))->toEqual([
					'foo' => [
						'bar'
					],
					'dmo_add_to_list' => [
						'input' => 'html',
						'label' => 'Add to whitelist',
						'html' => '<input type="checkbox" name="attachments[123][dmo_add_to_list]" >'
					]
				]);
			});
			it('marks "Add to whitelist" as checked if the default is set to the string "true"', function () {
				global $_SERVER;
				$_SERVER = [
					'SCRIPT_FILENAME' => ABSPATH . 'wp-admin/async-upload.php'
				];
				$fields = [
					'foo' => [
						'bar'
					]
				];
				$post = \Kahlan\Plugin\Double::instance([
					'class' => '\WP_Post'
				]);
				$post->ID = 123;
				allow('realpath')->toBeCalled()->andReturn(ABSPATH . 'wp-admin/async-upload.php');
				allow('get_option')->toBeCalled()->andReturn('true');
				allow('__')->toBeCalled()->andRun(function ($input) {
					return $input;
				});

				expect($this->upload->add_fields($fields, $post))->toEqual([
					'foo' => [
						'bar'
					],
					'dmo_add_to_list' => [
						'input' => 'html',
						'label' => 'Add to whitelist',
						'html' => '<input type="checkbox" name="attachments[123][dmo_add_to_list]" checked>'
					]
				]);
			});
		});
	});

	describe('->save_fields()', function () {
		context('the upload is not marked as to be added to the public allow list', function () {
			it('returns the post array and does nothing else', function () {
				$post = [
					'foo' => 'bar'
				];
				$attachment = [];

				expect('update_option')->not->toBeCalled();
				expect($this->upload->save_fields($post, $attachment))->toEqual($post);
			});
		});
		context('the upload is explicitly marked as not to be added to the public allow list', function () {
			it('returns the post array and does nothing else', function () {
				$post = [
					'foo' => 'bar'
				];
				$attachment = [
					'dmo_add_to_list' => false
				];

				expect('update_option')->not->toBeCalled();
				expect($this->upload->save_fields($post, $attachment))->toEqual($post);
			});
		});
		context('the upload is marked as to be added to the public allow list', function () {
			it('adds the attachment URL to the public allow list, then returns the post array', function () {
				$post = [
					'foo' => 'bar'
				];
				$attachment = [
					'dmo_add_to_list' => true,
					'url' => 'http://localhost/wp-content/upload-url'
				];
				allow('parse_url')->toBeCalled()->andReturn('http://localhost/wp-content/upload-url');
				allow('get_option')->toBeCalled()->andReturn("http://firstUrl\nhttp://secondUrl");
				allow('update_option')->toBeCalled();

				expect('update_option')->toBeCalled()->once()->with('dxw_members_only_list_content', "http://firstUrl\nhttp://secondUrl\nhttp://localhost/wp-content/upload-url");
				expect($this->upload->save_fields($post, $attachment))->toEqual($post);
			});
		});
	});
});
