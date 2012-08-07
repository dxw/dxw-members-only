<?php

function new_members_only_serve_uploads() {
  $req = $_SERVER['REQUEST_URI'];
  if ($req === '/wp-content/uploads' || startswith($req, '/wp-content/uploads/')) {

    $upload_dir = wp_upload_dir();
    $baseurl = preg_replace('%^https?://[^/]+(/.*)$%', '$1', $upload_dir['baseurl']);
    $basedir = $upload_dir['basedir'];
    $file = preg_replace("[^{$baseurl}]", $basedir, $req);

    if (is_file($file) && is_readable($file)) {
      $type = mime_content_type($file);
      header('Content-type: '.$type);
      echo file_get_contents($file);
      die();
    } else {
      new_members_only_redirect();
    }

  }
}

function new_members_only_redirect() {
  // Redirect
  $redirect = get_option('new_members_only_redirect');

  // %return_path%
  $redirect = str_replace('%return_path%', urlencode($_SERVER['REQUEST_URI']), $redirect);

  header('HTTP/1.1 303 See Other');
  header('Location: '.$redirect);
  die();
}

add_action('init', function () {
  if (
      defined('NEW_MEMBERS_ONLY_PASSTHROUGH') ||
      is_admin() ||
      is_user_logged_in() ||
      apply_filters('new_members_only_redirect', false) === true
     ) {
    new_members_only_serve_uploads();
    return;
  }

  // Get path component
  $path = $_SERVER['REQUEST_URI'];
  $pos = strpos($path, '?');
  if ($pos !== false)
    $path = substr($path, 0, $pos);

  // Always allow wp-login.php
  if ($path === '/wp-login.php') {
    return;
  }

  // List
  $hit = false;
  $list_type = get_option('new_members_only_list_type');
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

  if (($list_type === 'whitelist' && $hit) ||
      ($list_type === 'blacklist' && !$hit)) {
    new_members_only_serve_uploads();
    return;
  }

  new_members_only_redirect();
});
