<?php

add_action('init', 'new_members_only_redirect');

function new_members_only_redirect() {
  if (is_admin())          return;
  if (is_user_logged_in()) return;

  // Get path component
  $path = $_SERVER['REQUEST_URI'];
  $pos = strpos($path, '?');
  if ($pos !== false)
    $path = substr($path, 0, $pos);

  // Whitelist
  $whitelist = explode("\r\n",get_option('new_members_only_whitelist'));
  foreach ($whitelist as $w)
    if ($w === $path)
      return;

  // Redirect (die ATM)
  print_r($path);
  wp_die('');
}
