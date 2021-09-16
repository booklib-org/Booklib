#!/bin/sh
CONTAINER_READY="Startup_Init"
if [ ! -e $CONTAINER_READY ]; then
    if [ ! -e "/storage/.env"]; then
        is_first_time()
    fi
else
    not_first_time
fi

function is_first_time()
{
    touch $CONTAINER_READY
    echo "Running first-time startup logic"
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
}

function not_first_time()
{
    echo "Continuing."
}