<?php

$ms = new MetaSettings(__FILE__, 'new_members_only');
$ms->add_settings(__('New Members Only', 'membersonly'), array('list_type', 'list_content', 'ip_whitelist', 'redirect', 'redirect_root', 'upload_default', 'max_age'), 'new_members_only_options_page');

// Output settings page content
//
// @return void
function new_members_only_options_page()
{
    ?>
    <div class="wrap">
        <h2><?php _e('New Members Only', 'membersonly') ?></h2>

        <form method="post" action="options.php">
            <?php settings_fields('new_members_only') ?>

            <h3><?php _e('Content whitelist', 'membersonly'); ?></h3>
            <p><?php _e('Enter a list of content that all users can view without logging in.', 'membersonly'); ?></p>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="new_members_only_list_content"><?php _e('List of content URIs', 'membersonly') ?></label></th>
                    <td>
                        <textarea cols="30" rows="5" name="new_members_only_list_content" id="new_members_only_list_content" class="large-text code"><?php echo esc_html(get_option('new_members_only_list_content')) ?></textarea>
                        <br>
                        <span class="description"><?php _e('One host-relative URI per line. A * may be used at the end of a line. Query string ignored. /wp-login.php will always be allowed.', 'membersonly') ?></span>
                    </td>
                </tr>

            </table>

            <h3><?php _e('IP whitelist') ?></h3>
            <p><?php _e('Enter a list of IP addresses that can be allowed to view the site without logging in.', 'membersonly') ?></p>

            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><label for="new_members_only_ip_whitelist"><?php _e('List of IP addresses', 'membersonly') ?></label></th>
                    <td>
                        <textarea cols="30" rows="5" name="new_members_only_ip_whitelist" id="new_members_only_ip_whitelist" class="large-text code"><?php echo esc_html(get_option('new_members_only_ip_whitelist')) ?></textarea>
                        <br>
                        <span class="description"><?php _e('One IP address or CIDR address range per line.', 'membersonly') ?></span>
                    </td>
                </tr>

            </table>

            <h3><?php _e('Redirection', 'membersonly') ?></h3>
            <p><?php _e('In both the following options, <code>%return_path%</code> will be converted to the URL that was originally visited. i.e. <code>/wp-login.php?redirect_to=http://example.com/private-page</code>', 'membersonly') ?></p>

            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><label for="new_members_only_redirect"><?php _e('Redirect visitors to', 'membersonly') ?></label></th>
                    <td>
                        <input type="text" name="new_members_only_redirect" id="new_members_only_redirect" value="<?php form_option('new_members_only_redirect') ?>" class="regular-text">
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="new_members_only_redirect_root"><?php _e('Redirect visitors to / to', 'membersonly') ?></label></th>
                    <td>
                        <input type="text" name="new_members_only_redirect_root" id="new_members_only_redirect_root" value="<?php form_option('new_members_only_redirect_root') ?>" class="regular-text">
                        <span class="description"><?php _e("Only applies if / isn't whitelisted.", 'membersonly') ?></span>
                    </td>
                </tr>

            </table>

            <h3><?php _e('File uploads', 'membersonly') ?></h3>

            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><?php _e('New uploads by default are', 'membersonly') ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="new_members_only_upload_default" value="true" <?php echo get_option('new_members_only_upload_default') === 'true' ? 'checked' : '' ?>>
                                <?php _e('Added to list', 'membersonly') ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="new_members_only_upload_default" value="false" <?php echo get_option('new_members_only_upload_default') === 'true' ? '' : 'checked' ?>>
                                <?php _e('Not added to list', 'membersonly') ?>
                            </label>
                        </fieldset>
                        <span class="description"><?php _e('You can change this on a per-file basis when uploading', 'membersonly') ?></span>
                    </td>
                </tr>

            </table>

            <h3><?php _e('Max Age', 'membersonly') ?></h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="new_members_only_max_age"><?php _e('Max age for cache-control header', 'membersonly') ?></label></th>
                    <td>
                        <input type="number" min="0" step="1" name="new_members_only_max_age" id="new_members_only_max_age" value="<?php form_option('new_members_only_max_age') ?>" class="regular-text">
                        <span class="description">Defaults to 0 if not set.</span>
                    </td>
                </tr>
            </table>

            <?php submit_button() ?>
        </form>
    </div>
    <?php

}
