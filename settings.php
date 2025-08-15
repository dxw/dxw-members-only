<?php

add_action('init', 'dxw_members_only_metasettings', 10, 0);

function dxw_members_only_metasettings(): void
{
	/**
	 * @var dmometasettings $ms
	 */
	$ms = new dmometasettings(__FILE__, 'dxw_members_only');

	$ms->add_settings(__('dxw Members Only', 'dxwmembersonly'), [
	  'list_type',
	  'list_content',
	  'ip_whitelist',
	  'referrer_allow_list',
	  'redirect',
	  'redirect_root',
	  'upload_default',
	  'max_age',
	  'max_age_static',
	  'max_age_public'
	], 'dxw_members_only_options_page');
}

/**
 * Output settings page content
 *
 * @return void
 */
function dxw_members_only_options_page()
{
	?>
<div class="wrap">
  <h2><?php _e('dxw Members Only', 'dxwmembersonly') ?></h2>

  <form method="post" action="options.php">
    <?php settings_fields('dxw_members_only') ?>

    <h3><?php _e('Content whitelist', 'dxwmembersonly') ?></h3>
    <p><?php _e('Enter a list of content that all users can view without logging in.', 'dxwmembersonly') ?></p>

    <table class="form-table">
      <tr valign="top">
        <th scope="row"><label for="dxw_members_only_list_content"><?php _e('List of content URIs', 'dxwmembersonly') ?></label></th>
        <td>
          <textarea cols="30" rows="5" name="dxw_members_only_list_content" id="dxw_members_only_list_content" class="large-text code"><?php echo esc_html((string) get_option('dxw_members_only_list_content')) ?></textarea>
          <br>
          <span class="description"><?php _e('One host-relative URI per line. A * may be used at the end of a line. Query string ignored. /wp-login.php will always be allowed.', 'dxwmembersonly') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('IP whitelist') ?></h3>
    <p><?php _e('Enter a list of IP addresses that can be allowed to view the site without logging in.', 'dxwmembersonly') ?></p>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><label for="dxw_members_only_ip_whitelist"><?php _e('List of IP addresses', 'dxwmembersonly') ?></label></th>
        <td>
          <textarea cols="30" rows="5" name="dxw_members_only_ip_whitelist" id="dxw_members_only_ip_whitelist" class="large-text code"><?php echo esc_html((string) get_option('dxw_members_only_ip_whitelist')) ?></textarea>
          <br>
          <span class="description"><?php _e('One IPv4 address or CIDRv4 address range per line.', 'dxwmembersonly') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('Referrer Allow list') ?></h3>
    <p><?php _e('Enter a list of internal referrers to whitelist.', 'dxwmembersonly') ?></p>
    <p><?php _e('This is for enabling certain plugins such as Nelio AB to function correctly, do not use unless required', 'dxwmembersonly') ?></p>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><label for="dxw_members_only_referrer_allow_list"><?php _e('List of referrers', 'dxwmembersonly') ?></label></th>
        <td>
          <textarea cols="30" rows="5" name="dxw_members_only_referrer_allow_list" id="dxw_members_only_referrer_allow_list" class="large-text code"><?php echo esc_html((string) get_option('dxw_members_only_referrer_allow_list')) ?></textarea>
          <br>
          <span class="description"><?php _e('One address per line, do not include the domain (eg /admin.php?page=test)', 'dxwmembersonly') ?></span>
        </td>
      </tr>

  </table>
      <?php echo (string) get_option('dxw_members_only_referrer_whitelist'); ?>

    <h3><?php _e('Redirection', 'dxwmembersonly') ?></h3>
    <p><?php _e('In both the following options, <code>%return_path%</code> will be converted to the URL that was originally visited. i.e. <code>/wp-login.php?redirect_to=http://example.com/private-page</code>', 'dxwmembersonly') ?></p>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><label for="dxw_members_only_redirect"><?php _e('Redirect visitors to', 'dxwmembersonly') ?></label></th>
        <td>
          <input type="text" name="dxw_members_only_redirect" id="dxw_members_only_redirect" value="<?php form_option('dxw_members_only_redirect') ?>" class="regular-text">
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><label for="dxw_members_only_redirect_root"><?php _e('Redirect visitors to / to', 'dxwmembersonly') ?></label></th>
        <td>
          <input type="text" name="dxw_members_only_redirect_root" id="dxw_members_only_redirect_root" value="<?php form_option('dxw_members_only_redirect_root') ?>" class="regular-text">
          <span class="description"><?php _e("Only applies if / isn't whitelisted.", 'dxwmembersonly') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('File uploads', 'dxwmembersonly') ?></h3>

    <table class="form-table">

      <tr valign="top">
        <th scope="row"><?php _e('New uploads by default are', 'dxwmembersonly') ?></th>
        <td>
          <fieldset>
            <label>
              <input type="radio" name="dxw_members_only_upload_default" value="true" <?php echo get_option('dxw_members_only_upload_default') === 'true' ? 'checked' : '' ?>>
              <?php _e('Added to list', 'dxwmembersonly') ?>
            </label>
            <br>
            <label>
              <input type="radio" name="dxw_members_only_upload_default" value="false" <?php echo get_option('dxw_members_only_upload_default') === 'true' ? '' : 'checked' ?>>
              <?php _e('Not added to list', 'dxwmembersonly') ?>
            </label>
          </fieldset>
          <span class="description"><?php _e('You can change this on a per-file basis when uploading', 'dxwmembersonly') ?></span>
        </td>
      </tr>

    </table>

    <h3><?php _e('Browser Cache Max Age', 'dxwmembersonly') ?></h3>
    <p><?php _e('A Cache-Control header will be set that prevents server caching on non-public responses, but allows browsers to cache for a maximum number of seconds.', 'dxwmembersonly') ?></p>

    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="dxw_members_only_max_age"><?php _e('Browser cache max age in seconds', 'dxwmembersonly') ?></label></th>
            <td>
                <input type="number" min="0" step="1" name="dxw_members_only_max_age" id="dxw_members_only_max_age" value="<?php form_option('dxw_members_only_max_age') ?>" class="regular-text">
                <span class="description">Defaults to 0 if not set.</span>
            </td>
        </tr>
    </table>

    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="dxw_members_only_max_age_static"><?php _e('Browser cache max age in seconds (static content)', 'dxwmembersonly') ?></label></th>
            <td>
                <input type="number" min="0" step="1" name="dxw_members_only_max_age_static" id="dxw_members_only_max_age_static" value="<?php form_option('dxw_members_only_max_age_static') ?>" class="regular-text">
                <span class="description">Defaults to 0 if not set.</span>
            </td>
        </tr>
    </table>

    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="dxw_members_only_max_age_public"><?php _e('Browser cache max age in seconds (public content)', 'dxwmembersonly') ?></label></th>
            <td>
                <input type="number" min="0" step="1" name="dxw_members_only_max_age_public" id="dxw_members_only_max_age_public" value="<?php form_option('dxw_members_only_max_age_public') ?>" class="regular-text">
                <span class="description">Defaults to 86400 if not set.</span>
            </td>
        </tr>
    </table>

    <?php submit_button() ?>
  </form>
</div>
<?php
}
