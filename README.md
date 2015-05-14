# new-members-only

WordPress plugin that allows whitelisting certain content and certain IP addresses

## Usage

1. Install the plugin: cd wp-content/plugins && git clone https://github.com/dxw/new-members-only.git
2. Enable the plugin
3. Visit Settings > New Members Only
4. Add URLs to the whitelist
5. Add IP addresses to the whitelist or CIDR ranges (only IPv4 is supported at the moment) (i.e. 192.168.1.1 or 192.168.1.1/24)
6. Choose locations to redirect visitors to (usually /wp-login.php?redirect\_to=%return\_path%)
