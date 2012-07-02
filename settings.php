<?php

$ms = new MetaSettings(__FILE__, 'new_members_only');
$ms->add_settings(__('GCN Members Only'), array('blacklist', 'redirect'), 'new_members_only_options_page');

function new_members_only_options_page() {
?>
<div class="wrap">
  <h2><?php _e('GCN Members Only') ?></h2>

  <form method="post" action="options.php">
    <?php settings_fields('new_members_only') ?>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><label for="new_members_only_blacklist"><?php _e('Blacklist') ?></label></th>
        <td>
          <textarea cols="30" rows="5" name="new_members_only_blacklist" id="new_members_only_blacklist"><?php echo esc_html(get_option('new_members_only_blacklist')) ?></textarea>
          <br>
          <span class="description"><?php _e('One host-relative URI per line. A * may be used at the end of a line. Query string ignored.') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('Redirection') ?></h3>
    <p>In both the following options, <code>%return_path%</code> will be converted to the URL that was originally visited. i.e. <code>/wp-login.php?redirect_to=%return_path%</code></p>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><label for="new_members_only_redirect"><?php _e('Redirect visitors to') ?></label></th>
        <td>
          <input type="text" name="new_members_only_redirect" id="new_members_only_redirect" value="<?php form_option('new_members_only_redirect') ?>">
        </td>
      </tr>

    </table>

    <?php submit_button() ?>
  </form>
</div>
<?php
}
