<?php
/*
 * Plugin Name: New Members Only
 * Plugin URI: http://dxw.com
 * Description: Whitelist content that logged-out users can see
 * Version: 1.0
 * Author: dxw
 * Author URI: http://dxw.com
 */

function nmo_strip_query($path) {
  $pos = strpos($path, '?');
  if ($pos !== false) {
    $path = substr($path, 0, $pos);
  }

  return $path;
}

if (!defined('DOING_CRON')) {
  require(__DIR__.'/vendor.phar');
  require(__DIR__.'/metasettings.php');
  require(__DIR__.'/settings.php');
  require(__DIR__.'/upload.php');
  require(__DIR__.'/redirect.php');
}
