<?php

add_action('init', function () {
  $req = $_SERVER['REQUEST_URI'];
  if ($req === '/wp-content/uploads' || startswith($req, '/wp-content/uploads/')) {

    $upload_dir = wp_upload_dir();
    $baseurl = preg_replace('%^https?://[^/]+(/.*)$%', '$1', $upload_dir['baseurl']);
    $basedir = $upload_dir['basedir'];
    $file = preg_replace("[^{$baseurl}]", $basedir, $req);

    if (get_current_user_id() !== 0 && is_file($file) && is_readable($file)) {
      $type = mime_content_type($file);
      header('Content-type: '.$type);
      echo file_get_contents($file);
      die();
    } else {
      wp_die( __('403 Forbidden'), '', array('response' => 403) );
    }

  }
});
