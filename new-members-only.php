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
  require(dirname(__FILE__).'/metasettings.php');
  require(dirname(__FILE__).'/settings.php');
  require(dirname(__FILE__).'/upload.php');
  require(dirname(__FILE__).'/redirect.php');
}
