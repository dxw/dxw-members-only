<?php
/*
 * Plugin Name: New Members Only
 * Plugin URI: http://dxw.com
 * Description: Make your WordPress site visible to signed-in users only with the added ability to whitelist specific content for access by all users.
 * Version: 1.0
 * Author: dxw
 * Author URI: http://dxw.com
 * Text Domain: membersonly
 */

if (!defined('DOING_CRON')) {
  require(__DIR__.'/vendor.phar');
  require(__DIR__.'/helpers.php');
  require(__DIR__.'/metasettings.php');
  require(__DIR__.'/settings.php');
  require(__DIR__.'/upload.php');
  require(__DIR__.'/redirect.php');
}
