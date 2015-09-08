<?php
/**
 * MetaSettings Class
 * ??
 */
class MetaSettings {
  
  function __construct($file, $namespace) {
    $this->file = $file;
    $this->plugin = plugin_basename($file);
    $this->namespace = $namespace;

    $this->settings = (object)array();

    $this->set_defaults();
  }

  /**
   * Set default options settings for plugin
   */
  function set_defaults() {
    if(!get_option('new_members_only_list_content')) {
      add_option('new_members_only_list_content', '');
    }

    if(!get_option('new_members_only_redirect')) {
      add_option('new_members_only_redirect', '/wp-login.php?redirect_to=%return_path%');
    }

    if(!get_option('new_members_only_redirect_root')) {
      add_option('new_members_only_redirect_root', '/wp-login.php?redirect_to=%return_path%');
    }
  }

  /**
   * Wrapper for adding settings with the WP Settings API
   * 
   * @param string $title    Name of the setting
   * @param array  $options  Setting options array
   * @param string $callback Callback function
   */
  function add_settings($title, $options, $callback) {
    $this->settings->title = $title;
    $this->settings->options = $options;
    $this->settings->callback = $callback;

    add_filter('plugin_action_links', array($this,'plugin_action_links'), 10, 2 );
    add_action('admin_menu', array($this, 'admin_menu'));
    add_filter('whitelist_options', array($this, 'whitelist_options'));
  }

  /**
   * Insert action links for plugin 
   * 
   * @param  array  $links Existing action links for the plugin
   * @param  string $file  File to link to in the actions
   * @return array         New action links
   */
  function plugin_action_links($links, $file) {
    if (dirname($file) === dirname($this->plugin))
      array_unshift($links, '<a href="'.get_admin_url(null, 'options-general.php?page='.$this->plugin).'">'.__('Settings', 'membersonly').'</a>');
    return $links;
  }

  /**
   * Create admin menu for plugin
   * 
   * @return void
   */
  function admin_menu() {
    add_options_page($this->settings->title, $this->settings->title, 'edit_users', $this->file, $this->settings->callback);
  }

  /**
   * Prefix options with namespace
   * 
   * @param  array  $whitelist_options Options to whitelist
   * @return array                     Whitelisted options
   */
  function whitelist_options($whitelist_options) {
    $whitelist_options[$this->namespace] = array();
    foreach ($this->settings->options as $opt)
      $whitelist_options[$this->namespace][] = $this->namespace.'_'.$opt;
    return $whitelist_options;
  }
}
