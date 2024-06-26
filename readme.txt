=== dxw Members Only ===
Contributors: tomdxw, robdxw
Tags: membership, private content, security
Requires at least: 4.0
Tested up to: 6.5.5
Stable tag: 4.1.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Prevent users who aren't logged in from viewing your site. Allowlist selected content or IP addresses.

== Description ==

This plug-in allows site admins to make their site visible only to users who are
logged in. It also provides options to make selected URIs publicly available, and
to allowlist selected IP addresses so that they are not required to log in to view
protected content.

== Installation ==

1. Upload the plugin files to /wp-content/plugins/dxw-members-only, or install through the WordPress plugins interface
2. Activate the plugin
3. Visit Settings > dxw Members Only to set:
    - Any URIs that should be viewable by non-logged-in users
    - IP addresses that can view the site without logging in
    - Where to redirect visitors who are not logged in and try to view restricted content
    - Whether to automatically restrict access to uploads by default
    - A max age for the cache-control header that will be served to any users who try to access restricted content when not logged in

== Development ==

https://github.com/dxw/dxw-members-only

== Changelog ==

= 4.1.1 =
* For protected uploads, switch off output buffering and use `readfile()`

