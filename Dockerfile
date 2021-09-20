FROM alpine:3.14
LABEL Maintainer="Martijn Katerbarg <https://github.com/MKaterbarg/Booklib>"
LABEL Description="Full Stack Container with Booklib, Nginx, PHP8 on Alpine 3.14 - Needs MySQL to function, please read README."
VOLUME [ "/storage", "/library" ]

USER root

# Install packages and remove default server definition
RUN apk --no-cache add \
    python3 \
    curl \
    nginx \
    git \
    unrar \
    php8 \
    php8-ctype \
    php8-curl \
    php8-dom \
    php8-fpm \
    php8-gd \
    php8-intl \
    php8-json \
    php8-mbstring \
    php8-mysqli \
    php8-opcache \
    php8-openssl \
    php8-phar \
    php8-session \
    php8-xml \
    php8-xmlreader \
    php8-zlib \
    php8-zip \
    php8-fileinfo \
    php8-xmlwriter \
    php8-tokenizer \
    php8-pdo_mysql \
    imagemagick-dev \
    imagemagick \
    supervisor

# Create symlinks for programs that expect specific things
RUN ln -s /usr/bin/php8 /usr/bin/php

# Configure App Configs
COPY docker-conf/nginx.conf /etc/nginx/nginx.conf
COPY docker-conf/fpm.conf /etc/php8/php-fpm.d/www.conf
COPY docker-conf/php.ini /etc/php8/conf.d/custom.ini
COPY docker-conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-conf/policy.xml /etc/ImageMagick-7/policy.xml
RUN echo "* * * * * root php /Booklib/artisan schedule:run" >> /etc/crontab

# Setup application
RUN cd / && \
    git clone "https://github.com/MKaterbarg/Booklib.git" && \
    ln -s /Booklib/public /var/www/html

COPY docker-conf/empty /storage/thumb
COPY docker-conf/empty /storage/logs

RUN mkdir /Booklib/public/img && \
    ln -s /storage/thumb /Booklib/public/img/thumb && \
    rm -rf /Booklib/storage/logs && \
    ln -s /storage/logs /Booklib/storage/logs

COPY docker-conf/init.py /init.py

# Make sure files/folders needed by the processes are accessable when they run under the www-data user
RUN chown -hR nginx:www-data /Booklib/ && \
    chown -hR nginx:www-data /storage/ && \
    chown -hR nginx:www-data /run && \
    chown -hR nginx:www-data /var/lib/nginx && \
    chown -hR nginx:www-data /var/log/nginx && \
    chown -hR nginx:www-data /init.py

# Run Composer Stuff
RUN cd Booklib/ && \
    php composer.phar update && \
    php composer.phar install

# Final Staging
USER nginx
WORKDIR /Booklib
EXPOSE 8080

CMD [ "/init.py" ]
ENTRYPOINT [ "python3" ]

# Configure a healthcheck to validate that operating properly
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
