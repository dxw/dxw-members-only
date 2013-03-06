<?php

$ms = new MetaSettings(__FILE__, 'new_members_only');
$ms->add_settings(__('New Members Only'), array('list_type', 'list_content', 'ip_whitelist', 'redirect', 'redirect_root', 'upload_default'), 'new_members_only_options_page');

function new_members_only_options_page() {
?>
<div class="wrap">
  <h2><?php _e('New Members Only') ?></h2>

  <form method="post" action="options.php">
    <?php settings_fields('new_members_only') ?>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><?php _e('List type') ?></th>
        <td>
          <fieldset>
            <label>
              <input type="radio" name="new_members_only_list_type" value="blacklist" <?php echo get_option('new_members_only_list_type') === 'blacklist' ? 'checked' : '' ?>>
              <?php _e('Blacklist') ?>
            </label>
            <br>
            <label>
              <input type="radio" name="new_members_only_list_type" value="whitelist" <?php echo get_option('new_members_only_list_type') === 'whitelist' ? 'checked' : '' ?>>
              <?php _e('Whitelist') ?>
            </label>
          </fieldset>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="new_members_only_list_content"><?php _e('List') ?></label></th>
        <td>
          <textarea cols="30" rows="5" name="new_members_only_list_content" id="new_members_only_list_content" class="large-text code"><?php echo esc_html(get_option('new_members_only_list_content')) ?></textarea>
          <br>
          <span class="description"><?php _e('One host-relative URI per line. A * may be used at the end of a line. Query string ignored. /wp-login.php will always be allowed.') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('IP whitelist') ?></h3>
    <p><?php _e('Certain IP addresses can be allowed to view the site without logging in.') ?></p>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><label for="new_members_only_ip_whitelist"><?php _e('IP whitelist') ?></label></th>
        <td>
          <textarea cols="30" rows="5" name="new_members_only_ip_whitelist" id="new_members_only_ip_whitelist" class="large-text code"><?php echo esc_html(get_option('new_members_only_ip_whitelist')) ?></textarea>
          <br>
          <span class="description"><?php _e('One IPv4 address per line.') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('Redirection') ?></h3>
    <p><?php _e('In both the following options, <code>%return_path%</code> will be converted to the URL that was originally visited. i.e. <code>/wp-login.php?redirect_to=%return_path%</code>') ?></p>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><label for="new_members_only_redirect"><?php _e('Redirect visitors to') ?></label></th>
        <td>
          <input type="text" name="new_members_only_redirect" id="new_members_only_redirect" value="<?php form_option('new_members_only_redirect') ?>" class="regular-text">
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="new_members_only_redirect_root"><?php _e('Redirect visitors to / to') ?></label></th>
        <td>
          <input type="text" name="new_members_only_redirect_root" id="new_members_only_redirect_root" value="<?php form_option('new_members_only_redirect_root') ?>" class="regular-text">
          <span class="description"><?php _e('Only applies in whitelist mode or if "/" is blacklisted.') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('File uploads') ?></h3>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><?php _e('New uploads by default are') ?></th>
        <td>
          <fieldset>
            <label>
              <input type="radio" name="new_members_only_upload_default" value="true" <?php echo get_option('new_members_only_upload_default') === 'true' ? 'checked' : '' ?>>
              <?php _e('Added to list') ?>
            </label>
            <br>
            <label>
              <input type="radio" name="new_members_only_upload_default" value="false" <?php echo get_option('new_members_only_upload_default') === 'true' ? '' : 'checked' ?>>
              <?php _e('Not added to list') ?>
            </label>
          </fieldset>
          <span class="description"><?php _e('You can change this on a per-file basis when uploading') ?></span>
        </td>
      </tr>

    </table>

    <?php submit_button() ?>
  </form>
</div>
<?php
}
