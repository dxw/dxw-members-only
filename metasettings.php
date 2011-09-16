<?php

class MetaSettings {
  function __construct($file, $namespace) {
    $this->file = $file;
    $this->plugin = plugin_basename($file);
    $this->namespace = $namespace;

    $this->settings = (object)array();
  }

  function add_settings($title, $options, $callback) {
    $this->settings->title = $title;
    $this->settings->options = $options;
    $this->settings->callback = $callback;

    add_filter('plugin_action_links', array($this,'plugin_action_links'), 10, 2 );
    add_action('admin_menu', array($this, 'admin_menu'));
    add_filter('whitelist_options', array($this, 'whitelist_options'));
  }

  function plugin_action_links($links, $file) {
    // Assumption: the plugin will be in its own directory, not a single file inside wp-content/plugins/
    if (dirname($file) === dirname($this->plugin))
      array_unshift($links, '<a href="'.get_admin_url(null, 'options-general.php?page='.$this->plugin).'">'.__("Settings", $plugin).'</a>');
    return $links;
  }

  function admin_menu() {
    add_options_page($this->settings->title, $this->settings->title, 8, $this->file, $this->settings->callback);
  }

  function whitelist_options($whitelist_options) {
    $whitelist_options[$this->namespace] = array();
    foreach ($this->settings->options as $opt)
      $whitelist_options[$this->namespace][] = $this->namespace.'_'.$opt;
    return $whitelist_options;
  }
}
