<?php

/**
 * Handle request for uploaded content
 * @return void
 */
function dxw_members_only_serve_uploads()
{
    $req = dmo_strip_query($_SERVER['REQUEST_URI']);
    if (
        str_contains($req, '/wp-content/uploads/') || str_contains($req, '/wp-content/blogs.dir/')
    ) {
        $upload_dir = wp_upload_dir();
        $baseurl = preg_replace('%^https?://[^/]+(/.*)$%', '$1', $upload_dir['baseurl']);
        $basedir = $upload_dir['basedir'];
        $file = preg_replace("[^{$baseurl}]", $basedir, $req);
        $realFilePath = realpath($file);
        $realUploadDir = realpath($basedir);

        if (is_file($file) && is_readable($file) && \Missing\Strings::startsWith($realFilePath, $realUploadDir.'/')) {
            $mime = wp_check_filetype($file);

            $type = 'application/octet-stream';
            if ($mime['type'] !== false) {
                $type = $mime['type'];
            }

            header('Content-type: ' . $type);
            echo file_get_contents($file);
            die();
        }
    }
}
/**
 * Handle redirect for users when not logged in or viewing whitelisted content
 *
 * @param  boolean $root Whether attempting to view root or not
 * @return void
 */
function dxw_members_only_redirect($root)
{
    // Redirect
    if ($root) {
        $redirect = get_option('dxw_members_only_redirect_root');
    } else {
        $redirect = get_option('dxw_members_only_redirect');
    }

    // %return_path%
    $redirect = str_replace('%return_path%', urlencode($_SERVER['REQUEST_URI']), $redirect);

    header('HTTP/1.1 303 See Other');
    header('Location: '.$redirect);
    die();
}

function dxw_members_only_ip_in_range($ip, $range)
{
    $range = trim($range);

    # Handle IPv4-mapped IPv6 addresses
    if (preg_match('_^::ffff:(.*)$_', $ip, $m)) {
        $ip = $m[1];
    }

    $result = \Dxw\CIDR\IP::contains($range, $ip);
    if ($result->isErr()) {
        return false;
    }

    return $result->unwrap();
}

function dxw_members_only_current_ip_in_whitelist()
{
    $ip_list = explode("\n", get_option('dxw_members_only_ip_whitelist'));
    foreach ($ip_list as $ip) {
        $ip = trim($ip);
        if (!empty($ip) && dxw_members_only_ip_in_range($_SERVER['REMOTE_ADDR'], $ip)) {
            return true;
        }
    }
    return false;
}

function dxw_members_only_referrer_in_allow_list()
{
    $referrer_list = explode("\n", get_option('dxw_members_only_referrer_allow_list'));
    /*
     * If there is no referrer header, or if we have no configured referrers to
     * whitelist we can stop here.
     */
    if (isset($_SERVER['HTTP_REFERER'])) {
        foreach ($referrer_list as $referrer) {
            if (!empty($referrer)) {
                /*
                 * Add the site url to the referrer string to ensure that external
                 * referrers can't be used here.
                 */
                $whitelisted_referrer = get_site_url() . $referrer;
                $referrer_check = strpos($_SERVER['HTTP_REFERER'], $whitelisted_referrer);
                /*
                 * Check that there is a match, and that match is at the start of the referrer string.
                 * This is to ensure that the referrer being whitelisted can't be fooled by having
                 * a whitelisted referrer passed in as a parameter on the referrer string.
                 */
                if ($referrer_check !== false && $referrer_check == 0) {
                    return true;
                }
            }
        }
    }
    return false;
}

add_action('init', function () {
    // Fix for wp-cli
    if (defined('WP_CLI_ROOT')) {
        return;
    }

    $max_age = absint(get_option('dxw_members_only_max_age'));

    do_action('dxw_members_only_redirect');
    if (
        defined('dxw_members_ONLY_PASSTHROUGH') ||
        is_user_logged_in() ||
        apply_filters('dxw_members_only_redirect', false) === true
        ) {
        header('Cache-Control: private, max-age=' . $max_age);
        dxw_members_only_serve_uploads();
        return;
    }

    // Get path component
    $path = dmo_strip_query($_SERVER['REQUEST_URI']);

    // Always allow /wp-login.php
    if (\Missing\Strings::endsWith($path, 'wp-login.php')) {
        return;
    }

    // Always allow POST /wp-admin/admin-ajax.php with action=heartbeat
    if (\Missing\Strings::endsWith($path, 'wp-admin/admin-ajax.php') && isset($_POST['action']) && $_POST['action'] === 'heartbeat') {
        return;
    }

    // IP whitelist
    if (dxw_members_only_current_ip_in_whitelist()) {
        header('Cache-Control: private, max-age=' . $max_age);
        dxw_members_only_serve_uploads();
        return;
    }

    // Referrer whitelist
    if (dxw_members_only_referrer_in_allow_list()) {
        header('Cache-Control: private, max-age=' . $max_age);
        dxw_members_only_serve_uploads();
        return;
    }

    // List
    $hit = false;
    $list = explode("\n", get_option('dxw_members_only_list_content'));

    foreach ($list as $w) {
        $w = trim($w);

        if (empty($w)) {
            continue;
        }

        # /welcome => /welcome, /welcome/
        if ($path === $w || $path === $w . '/') {
            $hit = true;
            break;
        }

        # /welcome/ => /welcome
        if (\Missing\Strings::endsWith($w, '/') && $path === substr($w, 0, -1)) {
            $hit = true;
            break;
        }

        # /welcome/* => /welcome
        if (\Missing\Strings::endsWith($w, '/*') && $path === substr($w, 0, -2)) {
            $hit = true;
            break;
        }

        # /welcome/* => /welcome/.*
        if (\Missing\Strings::endsWith($w, '*') && \Missing\Strings::startsWith($path, substr($w, 0, -1))) {
            $hit = true;
            break;
        }
    }

    if ($hit) {
        header('Cache-Control: public');
        dxw_members_only_serve_uploads();
        return;
    }

    header('Cache-Control: private, max-age=' . $max_age);
    dxw_members_only_redirect($path === '/');
}, -99999999999);
