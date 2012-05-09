<?php
/*
 * Plugin Name: GCN Members Only
 * Plugin URI: http://dxw.com
 * Description: Forces users to log in. Visit the settings page for options.
 * Version: 1.0
 * Author: dxw
 * Author URI: http://dxw.com
 */
if (!defined('DOING_CRON')) {
  require(dirname(__FILE__).'/metasettings.php');
  require(dirname(__FILE__).'/settings.php');
  require(dirname(__FILE__).'/redirect.php');
}
