#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p var/cache var/log
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

	if [ "$APP_ENV" != 'prod' ]; then
		ln -sf ${PHP_INI_DIR}/php.ini-development ${PHP_INI_DIR}/php.ini
		composer install --prefer-dist --no-progress --no-suggest --no-interaction
	else
		ln -sf ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini
	fi

	>&2 echo "Waiting for DB to be ready"
	until nc -z -v -w30 $DATABASE_HOST $DATABASE_PORT
	do
	  echo -n "."
	  sleep 1
	done
	echo ""


	if [ "$APP_ENV" != 'prod' ]; then
		bin/console doctrine:schema:update --force --no-interaction
	fi
fi

exec docker-php-entrypoint "$@"
