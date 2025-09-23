ARG COUCHBASE_VERSION=3.2.2

FROM deminy/php-couchbase:${COUCHBASE_VERSION}-php8.1

COPY ./check.php /var/www/

CMD ["php", "./check.php"]
