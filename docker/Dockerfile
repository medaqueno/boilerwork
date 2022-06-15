FROM openswoole/swoole:4.11.1-php8.1

RUN apt-get update && apt-get install vim -y && \
    apt-get install openssl -y && \
    apt-get install libssl-dev -y && \
    apt-get install wget -y && \
    apt-get install git -y && \
    apt-get install procps -y && \
    apt-get install libz-dev -y && \
    apt-get install libmpdec-dev -y && \
    apt-get install libpq-dev -y && \
    apt-get install htop -y

RUN set -ex \
    && pecl update-channels \
    && pecl install redis-stable \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pcntl \
    # PHP Data Structures Extension.  Added in .ini. https://www.php.net/manual/es/book.ds.php
    && pecl install ds \
    # PHP Decimal Extension. Added in .ini. https://php-decimal.io/
    && pecl install decimal

RUN apt-get autoremove -y && apt-get clean && apt-get purge && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

COPY ./docker/rootfilesystem/ /
COPY ./src/ /var/www

RUN ["chmod", "+x", "/customized-entrypoint.sh"]

ENTRYPOINT ["/customized-entrypoint.sh"]


