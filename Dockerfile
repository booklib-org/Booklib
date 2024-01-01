FROM alpine:3.19
LABEL Maintainer="Martijn Katerbarg <https://github.com/booklib-org/booklib>"
LABEL Description="Full Stack Container with Booklib, Nginx, PHP8 on Alpine 3.14 - Needs MySQL to function, please read README."
VOLUME [ "/storage", "/library" ]

USER root

# Install packages and remove default server definition
RUN apk --no-cache add \
    python3 \
    curl \
    nginx \
    git \
    php83 \
    php83-ctype \
    php83-curl \
    php83-dom \
    php83-fpm \
    php83-gd \
    php83-intl \
    php83-json \
    php83-mbstring \
    php83-mysqli \
    php83-opcache \
    php83-openssl \
    php83-phar \
    php83-session \
    php83-xml \
    php83-xmlreader \
    php83-zlib \
    php83-zip \
    php83-fileinfo \
    php83-xmlwriter \
    php83-tokenizer \
    php83-pdo_mysql \
    imagemagick-dev \
    imagemagick \
    make \
    build-base \
    7zip \
    par2cmdline \
    python3 \
    supervisor

# Create symlinks for programs that expect specific things
RUN ln -s /usr/bin/php83 /usr/bin/php

RUN curl -o /tmp/unrar.tar.gz -L "https://www.rarlab.com/rar/unrarsrc-6.2.6.tar.gz"
RUN mkdir /tmp/unrar && tar xf /tmp/unrar.tar.gz -C /tmp/unrar --strip-components=1 && cd /tmp/unrar && make && install -v -m755 unrar /usr/bin

# Configure App Configs
COPY docker-conf/nginx.conf /etc/nginx/nginx.conf
COPY docker-conf/fpm.conf /etc/php8/php-fpm.d/www.conf
COPY docker-conf/php.ini /etc/php8/conf.d/custom.ini
COPY docker-conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-conf/policy.xml /etc/ImageMagick-7/policy.xml
RUN echo "* * * * * root php /Booklib/artisan schedule:run" >> /etc/crontab

# Setup application
RUN cd / && \
    git clone "https://github.com/booklib-org/booklib.git" /Booklib && \
    ln -s /Booklib/public /var/www/html

RUN mkdir /storage
RUN mkdir /storage/thumb
RUN mkdir /storage/logs

RUN mkdir /Booklib/public/img && \
    ln -s /storage/thumb /Booklib/public/img/thumb && \
    ln -s /storage/logs /Booklib/storage/logs

COPY docker-conf/entrypoint.sh /entrypoint.sh

# Make sure files/folders needed by the processes are accessable when they run under the www-data user
RUN chown -hR nginx:www-data /Booklib/ && \
    chown -hR nginx:www-data /storage/ && \
    chown -hR nginx:www-data /run && \
    chown -hR nginx:www-data /var/lib/nginx && \
    chown -hR nginx:www-data /var/log/nginx && \
    chown -hR nginx:www-data /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Run Composer Stuff
RUN cd /Booklib/ && \
    php composer.phar update && \
    php composer.phar install

# Final Staging
USER nginx
WORKDIR /Booklib
EXPOSE 8080

CMD [ "/entrypoint.sh" ]

# Configure a healthcheck to validate that operating properly
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
