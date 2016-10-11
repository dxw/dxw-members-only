=== dxw Members Only ===
Contributors: tomdxw, robdxw
Tags: membership, private content, security
Requires at least: 4.0
Tested up to: 4.6.1
Stable tag: 3.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Prevent users who aren't logged in from viewing your site. Whitelist selected content or IPs.

== Description ==

This plug-in allows site admins to make their site visible only to users who are
logged in. It also provides options to make selected URIs publicly available, and
to whitelist selected IPs so that they are not required to log in to view protected
content.

== Installation ==

1. Upload the plugin files to /wp-content/plguins/dxw-members-only, or install through the WordPress plugins interface
2. Activate the plugin
3. Visit Settings > dxw Members Only to set:
    - Any URIs that should be viewable by non-logged-in users
    - IPs that can view the site without logging in
    - Where to redirect visitors who are not logged in and try to view restricted content
    - Whether to automatically restrict access to uploads by default
    - A max age for the cache-control header that will be served to any users who try to access restricted content when not logged in

== Development ==

The source code is hosted at https://github.com/dxw/new-members-only.

The plug-in uses [phar-install](https://github.com/dxw/phar-install) to wrap all
composer dependencies into a single vendor.phar file.

To create a development build:
1. Run `composer install` to download dependencies
2. Run `vendor/bin/phar-install` to create the vendor.phar file
