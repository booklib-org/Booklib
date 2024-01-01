#!/bin/bash

# Seed the .env file if there is no file present
if [ ! -f "/storage/.env" ]; then
  cat /Booklib/.env.example | envsubst > /storage/.env
  php artisan key:generate
fi

copy /storage/.env /Booklib/.env

# Run PHP preparation commands
php artisan migrate
php artisan db:seed

# Set permissions for logging folder
chmod -R 777 /Booklib/storage

# Start supervisord and services
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
