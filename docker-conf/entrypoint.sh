#!/bin/bash


if mountpoint -q /storage; then

   if [ ! -d "/storage/thumb" ]; then
       mkdir -p /storage/thumb
   fi
   if [ ! -d "/storage/logs" ]; then
          mkdir -p /storage/logs
      fi
    if [ ! -d "/booklib/public/img" ]; then
       mkdir -p /booklib/public/img
   fi

    ln -s /storage/thumb /booklib/public/img/thumb
    rm -rf /booklib/storage/logs
    ln -s /storage/logs /booklib/storage

fi

# Seed the .env file if there is no file present
if [ ! -f "/storage/.env" ]; then
  cat /booklib/.env.example > /booklib/.env
  php artisan key:generate

  mv /booklib/.env /storage/.env

fi

cp /storage/.env /booklib/.env
php artisan config:cache
service cron start

# Run PHP preparation commands
php artisan migrate
php artisan db:seed

# Set permissions for logging folder
chmod -R 777 /booklib/storage
chmod  777 /booklib/public/img/thumb

# Start supervisord and services
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
