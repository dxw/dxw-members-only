<?php

add_action('init', 'new_members_only_redirect');

if (!function_exists('startswith')) {
  function startswith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
  }
}

if (!function_exists('endswith')) {
  function endswith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
  }
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

  // Blacklist
  $blacklist = explode("\r\n",get_option('new_members_only_blacklist'));
  foreach ($blacklist as $w) {
    $w = trim($w);

    if(empty($w)) {
      continue;
    }

    # /welcome => /welcome, /welcome/
    if ($path === $w || $path === $w . '/') {
      break;
    }

    # /welcome/ => /welcome
    if (endswith($w, '/') && $path === substr($w, 0, -1)) {
      break;
    }

    # /welcome/* => /welcome
    if (endswith($w, '/*') && $path === substr($w, 0, -2)) {
      break;
    }

    # /welcome/* => /welcome/.*
    if (endswith($w, '*') && startswith($path, substr($w, 0, -1))) {
      break;
    }

    return;
  }

  // Redirect
  $redirect = get_option('new_members_only_redirect');

  // %return_path%
  $redirect = str_replace('%return_path%', urlencode($_SERVER['REQUEST_URI']), $redirect);

  header('HTTP/1.1 303 See Other');
  header('Location: '.$redirect);
  die();
}
