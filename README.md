# dxw-members-only

This plug-in allows site admins to make their site visible only to users who are
logged in. It also provides options to make selected URIs publicly available, and
to whitelist selected IPs so that they are not required to log in to view protected
content.

## Usage

1. Install the plugin: cd wp-content/plugins && git clone https://github.com/dxw/dxw-members-only.git
2. Enable the plugin
3. Visit Settings > dxw Members Only
4. Add URLs to the whitelist
5. Add IP addresses to the whitelist or CIDR ranges (i.e. `192.168.1.1` or `192.168.1.1/24` or `2001:db8::/64`)
6. Choose locations to redirect visitors to (usually /wp-login.php?redirect\_to=%return\_path%)
7. Set max age for the cache-control header that will be served to any users who try to access restricted content when not logged in

## Development

To create a development build:
1. Run `composer install` to download dependencies
