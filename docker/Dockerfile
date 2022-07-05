FROM openswoole/swoole:4.11.1-php8.1

ENV LIBRDKAFKA_VERSION v1.9.0

RUN apt-get update && apt-get install vim -y && \
    apt-get install openssl -y && \
    apt-get install libssl-dev -y && \
    apt-get install wget -y && \
    apt-get install git -y && \
    apt-get install procps -y && \
    apt-get install libz-dev -y && \
    apt-get install libmpdec-dev -y && \
    apt-get install libpq-dev -y && \
    apt-get install htop -y && \
    apt-get install librdkafka-dev -y

RUN git clone https://github.com/edenhill/librdkafka.git && \
    cd librdkafka && \
    ./configure && \
    make && make install

RUN set -ex \
    && pecl update-channels \
    && pecl install redis-stable && docker-php-ext-enable redis \
    && docker-php-ext-install pcntl \
    # PHP Data Structures Extension.  Added in .ini. https://www.php.net/manual/es/book.ds.php
    && pecl install ds \
    && pecl install rdkafka && docker-php-ext-enable rdkafka

RUN apt-get autoremove -y && apt-get clean && apt-get purge && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

COPY ./docker/rootfilesystem/ /
COPY ./ /var/www

RUN ["chmod", "+x", "/customized-entrypoint.sh"]

ENTRYPOINT ["/customized-entrypoint.sh"]
