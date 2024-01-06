FROM php:8.3-fpm
LABEL Maintainer="Martijn Katerbarg <https://github.com/booklib-org/booklib>"
LABEL Description="Full Stack Container with Booklib, Nginx, PHP8.3 on Alpine 3.19 - Needs MySQL to function, please read README."
VOLUME [ "/storage", "/library" ]
ARG DEBIAN_FRONTEND=noninteractive

# Default Env
ENV \
  APP_NAME="Booklib" \
  APP_ENV="local" \
  APP_DEBUG="false" \
  LOG_CHANNEL="stack" \
  LOG_DEPRECATIONS_CHANNEL="null" \
  LOG_LEVEL="debug" \
  BROADCAST_DRIVER="log" \
  CACHE_DRIVER="file" \
  FILESYSTEM_DISK="local" \
  QUEUE_CONNECTION="sync" \
  SESSION_DRIVER="file" \
  SESSION_LIFETIME="120" \
  UPLOAD_LIMIT="1024M" \
  PHP_UPLOAD_LIMIT="1024M" \
  MEMORY_LIMIT="1024M" \
  PHP_MEMORY_LIMIT="1024M"

# Add in code
ADD . /booklib/



# Install packages and remove default server definition
RUN apt-get -qq update \
    && apt-get -y upgrade\
    && apt-get install -y \
    python3 \
    nginx \
    curl \
    cron \
    git \
    pdftk \
    make \
    libzip-dev \
    build-essential \
    libpq-dev \
    7zip \
    par2 \
    imagemagick \
    libmagickcore-dev \
    libgd-dev \
    zlib1g \
    libpng-dev \
    zlib1g-dev \
    libpng-tools \
    supervisor

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pgsql
RUN docker-php-ext-configure pdo_pgsql && docker-php-ext-install pdo_pgsql
RUN docker-php-ext-configure gd --with-jpeg --with-webp && docker-php-ext-install gd
RUN docker-php-ext-configure bcmath && docker-php-ext-install bcmath
RUN docker-php-ext-configure zip && docker-php-ext-install zip
RUN docker-php-ext-configure pdo_mysql && docker-php-ext-install pdo_mysql
RUN docker-php-ext-configure mysqli && docker-php-ext-install mysqli
RUN docker-php-ext-configure opcache && docker-php-ext-install opcache
RUN docker-php-ext-configure intl && docker-php-ext-install intl

RUN sed -i \
        -e "s/;listen.mode = 0660/listen.mode = 0666/g" \
        -e "s/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/g" \
        /usr/local/etc/php-fpm.d/www.conf

RUN curl -o /tmp/unrar.tar.gz -L "https://www.rarlab.com/rar/unrarsrc-6.2.6.tar.gz"
RUN mkdir /tmp/unrar && tar xf /tmp/unrar.tar.gz -C /tmp/unrar --strip-components=1 && cd /tmp/unrar && make && install -v -m755 unrar /usr/bin

RUN cd /booklib \
  && php composer.phar install \
  # Set permissions
  && chown -R www-data:www-data /booklib \
  # Cleanup
  && apt-get autoremove -y \
  && apt-get clean \
  && echo 'memory_limit = 4096M' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini \
  && echo 'upload_max_filesize = 1024M' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini \
  && echo 'post_max_size = 1024M' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini \
  && rm -rf \
    /tmp/* \
    /var/lib/apt/lists/* \
    /var/tmp/

ADD ./docker-conf/entrypoint.sh /entrypoint.sh
ADD ./docker-conf/supervisord.conf /etc/supervisord.conf
ADD ./docker-conf/nginx.conf /etc/nginx/nginx.conf
ADD ./docker-conf/policy.xml /etc/ImageMagick-6/policy.xml

RUN echo "* * * * * root php /booklib/artisan schedule:run" >> /etc/crontab

RUN chmod +x /entrypoint.sh

RUN rm -rf /booklib/.git && rm -rf /booklib/.github

EXPOSE 80
WORKDIR /booklib
CMD ["/entrypoint.sh"]
