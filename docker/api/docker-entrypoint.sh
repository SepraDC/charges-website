#!/bin/sh

APP_UID=${APP_UID:-1000}

if [[ -z "$APP_UID" ]]
then
    >&2 echo "You must set the app user id"
    exit 1
fi

adduser app -s /bin/sh -h /home/app -u "$APP_UID" -D
mkdir -p /home/app /var/www/var/media /var/www/public/media
chown app:app -R /home/app /var/www/var /var/www/var/media /var/www/public/media

echo "Warmup cache"
su -c "php /var/www/bin/console cache:clear -n" app


# Composer install (only if not in prod env)
if [[ "prod" != "$APP_ENV" ]]
then
  echo "Installing dependencies ..."
  su -c "composer install " app
  su -c "vendor/bin/codecept build " app
fi

# Ensure that the key pair for JWT are created
if [ ! -f /var/www/config/jwt/private.pem ]
then
  echo "Generate key pair fot jwt authentication"
  mkdir -p /var/www/config/jwt
  chown app:app /var/www/config/jwt
  # generate keys
  su -c "php /var/www/bin/console lexik:jwt:generate-keypair -n --overwrite" app
fi

# Wait for api
if grep -q ^DATABASE_URL= .env.local; then
  echo "Waiting for db to be ready..."
  ATTEMPTS_LEFT_TO_REACH_DATABASE=60
  until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(bin/console dbal:run-sql "SELECT 1" 2>&1); do
    if [ $? -eq 255 ]; then
      # If the Doctrine command exits with 255, an unrecoverable error occurred
      ATTEMPTS_LEFT_TO_REACH_DATABASE=0
      break
    fi
    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
    echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
  done

  if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
    echo "The database is not up or not reachable:"
    echo "$DATABASE_ERROR"
    exit 1
  else
    echo "The db is now ready and reachable"
  fi

  su -c "php /var/www/bin/console doctrine:migrations:migrate -n -vv" app

  if [[ "prod" != "$APP_ENV" ]]
  then
    su -c "php /var/www/bin/console doctrine:database:create --if-not-exists -n -vv -e test" app
    su -c "php /var/www/bin/console doctrine:migrations:migrate -n -vv -e test" app
  fi
else
  echo 'No database config found. Skipping database setup steps'
fi

if [[ "prod" == "$APP_ENV" ]]
then
  # set opcache.preloading config
  echo "Configuring opcache.preload ..."
  echo "opcache.preload_user=/var/www/config/preload.php" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
fi

# Add an alias for debugging
echo "alias php-debug='php -d xdebug.mode=debug -d xdebug.client_host=$XDEBUG_CLIENT_HOST'" > /home/app/.bashrc

# launch supervisor
#echo "Launching supervisor ..."
#supervisord
# launch nginx
echo "Launching Nginx ..."
mkdir -p /run/nginx
nginx
# launch php-fpm
echo "Launching php-fpm ..."
php-fpm
