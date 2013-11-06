<?php

add_filter('attachment_fields_to_edit', function ($fields, $post) {

  if (realpath($_SERVER['SCRIPT_FILENAME']) === ABSPATH.'wp-admin/async-upload.php') {
    $upload_default = get_option('new_members_only_upload_default');

    $fields['nmo_add_to_list'] = array(
      'input' => 'html',
      'label' => __('Add to whitelist'),
      'html' => '<input type="checkbox" name="attachments['.$post->ID.'][nmo_add_to_list]" '.($upload_default==='true'?'checked':'').'>',
    );
  }

  return $fields;
}, 10, 2);

add_filter('attachment_fields_to_save', function ($post, $attachment) {
  if (isset($attachment['nmo_add_to_list']) && $attachment['nmo_add_to_list']) {
    $url = parse_url($attachment['url'], PHP_URL_PATH);

    $list_content = get_option('new_members_only_list_content');
    $list_content .= "\n" . $url;
    update_option('new_members_only_list_content', $list_content);
  }

  return $post;
}, 10, 2);
