<?php

namespace Dxw\MembersOnly;

class Upload implements \Dxw\Iguana\Registerable
{
	public function register(): void
	{
		add_filter('attachment_fields_to_edit', [$this, 'add_fields'], 10, 2);
		add_filter('attachment_fields_to_save', [$this, 'save_fields'], 10, 2);
	}

	public function add_fields(array $fields, \WP_Post $post): array
	{
		if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === ABSPATH.'wp-admin/async-upload.php') {
			$upload_default = (string) get_option('dxw_members_only_upload_default');

			$fields['dmo_add_to_list'] = [
				'input' => 'html',
				'label' => __('Add to whitelist', 'dxwmembersonly'),
				'html' => '<input type="checkbox" name="attachments['.$post->ID.'][dmo_add_to_list]" '.($upload_default === 'true' ? 'checked' : '').'>',
			];
		}

		return $fields;
	}

	public function save_fields(array $post, array $attachment): array
	{
		if (isset($attachment['dmo_add_to_list']) && $attachment['dmo_add_to_list']) {
			$url = parse_url((string) $attachment['url'], PHP_URL_PATH);

			$list_content = (string) get_option('dxw_members_only_list_content');
			$list_content .= "\n" . $url;
			update_option('dxw_members_only_list_content', $list_content);
		}

		return $post;
	}
}
