<?php

function clog($x) {
  echo '<script>console.log(';
  echo json_encode($x);
  echo ')</script>';
}

add_filter('attachment_fields_to_edit', function ($fields, $post) {
  clog('attachment_fields_to_edit');
  clog($fields);
  clog($post);

  $list_type = get_option('new_members_only_list_type');
  $upload_default = get_option('new_members_only_upload_default');

  $fields['nmo_add_to_list'] = array(
    'input' => 'html',
    'label' => $list_type === 'blacklist' ? _('Add to blacklist') : _('Add to whitelist'),
    'html' => '<input type="checkbox" name="attachments['.$post->ID.'][nmo_add_to_list]" '.($upload_default==='true'?'checked':'').'>',
  );
  return $fields;
}, 10, 2);

add_filter('attachment_fields_to_save', function ($post, $attachment) {
  if (isset($attachment['nmo_add_to_list']) && $attachment['nmo_add_to_list']) {
    $url = parse_url($attachment['url'], PHP_URL_PATH);

    $list_content = get_option('new_members_only_list_content');
    $list_content .= "\n" . $url;
    update_option('new_members_only_list_content', $list_content);
  }
}, 10, 2);
