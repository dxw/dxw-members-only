<?php
/*
 * Plugin Name: New Members Only
 * Plugin URI: http://dxw.com
 * Description: Whitelist or blacklist content from logged-out users. Also, block uploads dir from logged-out users. Do not disable.
 * Version: 1.0
 * Author: dxw
 * Author URI: http://dxw.com
 */

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

if (!defined('DOING_CRON')) {
  require(__DIR__.'/metasettings.php');
  require(__DIR__.'/settings.php');
  require(__DIR__.'/upload.php');
  require(__DIR__.'/Net_IPv4-1.3.4/Net/IPv4.php');
  require(__DIR__.'/redirect.php');
}
