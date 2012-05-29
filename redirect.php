<?php

add_action('init', 'new_members_only_redirect');

function startswith($haystack, $needle) {
  return substr($haystack, 0, strlen($needle)) === $needle;
}

function endswith($haystack, $needle) {
  return substr($haystack, -strlen($needle)) === $needle;
}

function new_members_only_redirect() {
  if (defined('NEW_MEMBERS_ONLY_PASSTHROUGH'))                   return;
  if (is_admin())                                                return;
  if (is_user_logged_in())                                       return;

  if (apply_filters('new_members_only_redirect', false) === true) return;

  // Get path component
  $path = $_SERVER['REQUEST_URI'];
  $pos = strpos($path, '?');
  if ($pos !== false)
    $path = substr($path, 0, $pos);

  // Whitelist
  $whitelist = explode("\r\n",get_option('new_members_only_whitelist'));
  foreach ($whitelist as $w) {
    $w = trim($w);

    if(empty($w)) {
      continue;
    }

    # /welcome => /welcome, /welcome/
    if ($path === $w || $path === $w . '/') {
      return;
    }

    # /welcome/ => /welcome
    if (endswith($w, '/') && $path === substr($w, 0, -1)) {
      return;
    }

    # /welcome/* => /welcome
    if (endswith($w, '/*') && $path === substr($w, 0, -2)) {
      return;
    }

    # /welcome/* => /welcome/.*
    if (endswith($w, '*') && startswith($path, substr($w, 0, -1))) {
      return;
    }
  }

  // Redirect
  if ($path === '/')
    $redirect = get_option('new_members_only_redirect_root');
  else
    $redirect = get_option('new_members_only_redirect_elsewhere');

  // %return_path%
  $redirect = str_replace('%return_path%', urlencode($_SERVER['REQUEST_URI']), $redirect);

  header('HTTP/1.1 303 See Other');
  header('Location: '.$redirect);
  die();
}
