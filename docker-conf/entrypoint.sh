#!/bin/bash

#Start with latest update
cd /Booklib; git pull

if mountpoint -q /storage; then

   if [ ! -d "/storage/thumb" ]; then
       mkdir -p /storage/thumb
   fi
   if [ ! -d "/storage/logs" ]; then
          mkdir -p /storage/logs
      fi

    ln -s /storage/thumb /Booklib/public/img/thumb
    ln -s /storage/logs /Booklib/storage/logs

fi

# Seed the .env file if there is no file present
if [ ! -f "/storage/.env" ]; then
  cat /Booklib/.env.example | envsubst > /storage/.env
  php artisan key:generate
fi

cd /Booklib/ ; php composer.phar update ; php composer.phar install

copy /storage/.env /Booklib/.env

# Run PHP preparation commands
php artisan migrate
php artisan db:seed

# Set permissions for logging folder
chmod -R 777 /Booklib/storage

# Start supervisord and services
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
