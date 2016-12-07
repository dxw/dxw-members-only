# dxw-members-only

This plug-in allows site admins to make their site visible only to users who are
logged in. It also provides options to make selected URIs publicly available, and
to whitelist selected IPs so that they are not required to log in to view protected
content.

## Usage

1. Install and enable the plugin
2. Visit Settings > dxw Members Only
3. Add URIs to the content whitelist. These URIs will be viewable by all users.
4. Add IP addresses or CIDR ranges to the IP whitelist (e.g. 192.168.1.1 or 192.168.1.1/24 or 2001:db8::/64)
5. Choose locations to redirect visitors to (usually /wp-login.php?redirect\_to=%return\_path%)
6. Set max age for the cache-control header that will be served to any users who try to access restricted content when not logged in

## Development

The plug-in uses [phar-install](https://github.com/dxw/phar-install) to wrap all
composer dependencies into a single vendor.phar file.

To create a development build:

1. Run `composer install` to download dependencies
2. Run `vendor/bin/phar-install` to create the vendor.phar file
