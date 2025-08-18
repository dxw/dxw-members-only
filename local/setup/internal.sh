#!/bin/sh
set -e

# This code will be run during setup, INSIDE the container.

##############
# Config
##############
title="dxw Members Only"

wp db reset --yes

wp core install --skip-email --admin_user=admin --admin_password=admin --admin_email=admin@localhost.invalid --url=http://localhost --title="$title"

wp theme activate twentytwentyfive

wp plugin activate dxw-members-only

wp core update-db
