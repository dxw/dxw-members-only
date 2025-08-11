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

### Testing locally

1. Install and activate this plugin on a development site
2. Ensure that requests to `wp-content/uploads` are redirected via PHP as follows (otherwise the plugin will not serve any cache headers for media library files):
   * Access the WordPress container (usually `script/console` for most of our projects)
   * `cd /etc/apache2/sites-enabled`
   * `vi wordpress.conf`, and add the following rewrite rule:
      ```
      RewriteRule ^wp-content/uploads/.* index.php [L]
      ```
   * Save & exit vi, then `service apache2 reload`
3. If you want to allow-list your local IP address within the plugin, add `0.0.0.0/0` to the IP allow list in the plugin settings page (this has the effect of allow-listing all IP addresses)

## Versioning

Please publish and tag new releases when they happen.

As well as the individual version tags, we also have a major version tag (currently v4) that tracks the latest release for that major version. That has to be manually updated after you've done the release on GitHub as follows:

(e.g. if you'd just published v4.5.0):

```sh
git checkout main
git fetch --tags -f
git tag -f v4 v4.5.0
git push origin -f --tags
```
