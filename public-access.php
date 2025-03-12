<?php

add_filter('attachment_fields_to_edit', 'add_public_access_flag', 10, 2);

function add_public_access_flag($form_fields, $post)
{
	$public_access = (bool) get_post_meta($post->ID, 'public_access', true);
	$input = '<input type="checkbox" id="attachments-'.$post->ID.'-public_access" name="attachments['.$post->ID.'][public_access]" value="1"'. checked($public_access, true, false) .'>';
	$form_fields['public_access'] = [
		'label' => 'Public',
		'input' => 'html',
		'html' => $input,
		'value' => $public_access,
		'helps' => 'Does the attachment contain wholly public info, such as a logo or stock art'
	];
	return $form_fields;
}

add_action('edit_attachment', 'save_public_access_flag');

function save_public_access_flag($attachment_id)
{
	if (isset($_REQUEST['attachments'][$attachment_id]['public_access'])) {
		$public_access = $_REQUEST['attachments'][$attachment_id]['public_access'];
		update_post_meta($attachment_id, 'public_access', $public_access);
	}
}
