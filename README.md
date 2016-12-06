# new-members-only

WordPress plugin that blocks access to site when user is not logged in. Allows for whitelisting of certain content and certain IP addresses.

## Usage

1. Install the plugin: cd wp-content/plugins && git clone https://github.com/dxw/new-members-only.git
2. Enable the plugin
3. Visit Settings > New Members Only
4. Add URLs to the whitelist
5. Add IP addresses to the whitelist or CIDR ranges (i.e. `192.168.1.1` or `192.168.1.1/24` or `2001:db8::/64`)
6. Choose locations to redirect visitors to (usually /wp-login.php?redirect\_to=%return\_path%)
