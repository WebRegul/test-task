#!/usr/bin/env bash

echo "=== "$(date --rfc-3339=ns)" set chmod -R 0775 /var/www/docker"
chmod -R 0775 /var/www/docker

echo "=== "$(date --rfc-3339=ns)" cd /var/www"
cd /var/www

echo "=== "$(date --rfc-3339=ns)" cp git pre-commit hook"
[ -e tools/pre-commit ] && cp tools/pre-commit .git/hooks/ && chmod 0775 .git/hooks/pre-commit

echo "=== "$(date --rfc-3339=ns)" enter to backend"
cd backend

echo "=== "$(date --rfc-3339=ns)" create storage/app/users"
mkdir -p storage/app/users

echo "=== "$(date --rfc-3339=ns)" composer install"
composer install

echo "=== "$(date --rfc-3339=ns)" artisan migrate"
php artisan migrate

echo "=== "$(date --rfc-3339=ns)" artisan DB:seed --class=TariffsSeeder"
php artisan DB:seed --class=TariffsSeeder
