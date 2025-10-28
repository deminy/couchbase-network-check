ARG COUCHBASE_VERSION=4.4.0
ARG PHP_VERSION=8.4

FROM deminy/php-couchbase:${COUCHBASE_VERSION}-php${PHP_VERSION}

COPY ./check.php /var/www/

CMD ["php", "./check.php"]
