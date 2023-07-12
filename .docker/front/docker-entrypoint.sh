#!/bin/sh
#
# This entrypoint is only used in the dev environment
#

APP_UID=${APP_UID:-1000}

if [[ -z "$APP_UID" ]]
then
    >&2 echo "You must set the APP_UID"
    exit 1
fi

usermod -u $APP_UID node
chmod 777 /srv/app

su -c "pnpm install" node
su -c "pnpm dev" node