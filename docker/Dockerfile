FROM openswoole/swoole:4.11.1-php8.1

ENV LIBRDKAFKA_VERSION v1.9.0

RUN apt-get update && apt-get install -y vim openssl libssl-dev wget git procps libz-dev libmpdec-dev libpq-dev inotify-tools htop librdkafka-dev \
    #
    && git clone https://github.com/edenhill/librdkafka.git && \
    cd librdkafka && \
    ./configure && \
    make && make install \
    #
    && set -ex \
    && pecl update-channels \
    && pecl install redis-stable && docker-php-ext-enable redis \
    && docker-php-ext-install pcntl \
    # PHP Data Structures Extension.  Added in .ini. https://www.php.net/manual/es/book.ds.php
    && pecl install ds \
    && pecl install rdkafka && docker-php-ext-enable rdkafka \
    && apt-get autoremove -y && apt-get clean && apt-get purge && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

COPY ./docker/rootfilesystem/ /
COPY ./src/ /var/www

RUN ["chmod", "+x", "/customized-entrypoint.sh"]

ENTRYPOINT ["/customized-entrypoint.sh"]
