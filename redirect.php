<?php

function new_members_only_serve_uploads() {
  $req = $_SERVER['REQUEST_URI'];
  if ($req === '/wp-content/uploads' || startswith($req, '/wp-content/uploads/')) {

    $upload_dir = wp_upload_dir();
    $baseurl = preg_replace('%^https?://[^/]+(/.*)$%', '$1', $upload_dir['baseurl']);
    $basedir = $upload_dir['basedir'];
    $file = preg_replace("[^{$baseurl}]", $basedir, $req);

    if (is_file($file) && is_readable($file)) {
      $mime = wp_check_filetype($file);

      $type = 'application/octet-stream';
      if ($mime['type'] !== false) {
        $type = $mime['type'];
      }

      header('Content-type: ' . $type);
      echo file_get_contents($file);
      die();
    }

  }
}

function new_members_only_redirect($root) {
  // Redirect
  if ($root) {
    $redirect = get_option('new_members_only_redirect_root');
  } else {
    $redirect = get_option('new_members_only_redirect');
  }

  // %return_path%
  $redirect = str_replace('%return_path%', urlencode($_SERVER['REQUEST_URI']), $redirect);

  header('HTTP/1.1 303 See Other');
  header('Location: '.$redirect);
  die();
}

function new_members_only_ip_in_range($ip, $range) {
  $range = trim($range);

  # Fix 4-in-6 addresses
  if (preg_match('_^::ffff:(.*)$_', $ip, $m)) {
    $ip = $m[1];
  }

  return \CIDR\IPv4::match($range, $ip);
}

function new_members_only_current_ip_in_whitelist() {
  $ip_list = explode("\r\n",get_option('new_members_only_ip_whitelist'));
  foreach ($ip_list as $ip) {
    if (!empty($ip) && new_members_only_ip_in_range($_SERVER['REMOTE_ADDR'], $ip)) {
      return true;
    }
  }

  return false;
}

add_action('init', function () {
  do_action('new_members_only_redirect');
  if (
      defined('NEW_MEMBERS_ONLY_PASSTHROUGH') ||
      is_admin() ||
      is_user_logged_in() ||
      apply_filters('new_members_only_redirect', false) === true
     ) {
    header('Cache-control: private');
    new_members_only_serve_uploads();
    return;
  }

  // Get path component
  $path = $_SERVER['REQUEST_URI'];
  $pos = strpos($path, '?');
  if ($pos !== false)
    $path = substr($path, 0, $pos);

  // Always allow wp-login.php
  if (endswith($path, 'wp-login.php')) {
    return;
  }

  // IP whitelist
  if (new_members_only_current_ip_in_whitelist()) {
    return;
  }

  // List
  $hit = false;
  $list = explode("\r\n",get_option('new_members_only_list_content'));

  foreach ($list as $w) {
    $w = trim($w);

    if(empty($w)) {
      continue;
    }

    # /welcome => /welcome, /welcome/
    if ($path === $w || $path === $w . '/') {
      $hit = true;
      break;
    }

    # /welcome/ => /welcome
    if (endswith($w, '/') && $path === substr($w, 0, -1)) {
      $hit = true;
      break;
    }

    # /welcome/* => /welcome
    if (endswith($w, '/*') && $path === substr($w, 0, -2)) {
      $hit = true;
      break;
    }

    # /welcome/* => /welcome/.*
    if (endswith($w, '*') && startswith($path, substr($w, 0, -1))) {
      $hit = true;
      break;
    }
  }

  if ($hit) {
    header('Cache-control: public');
    new_members_only_serve_uploads();
    return;
  }

  header('Cache-control: private');
  new_members_only_redirect($path === '/');
}, -99999999999);
