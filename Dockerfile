FROM dunglas/frankenphp

RUN install-php-extensions mysqli pdo_mysql

ENV SERVER_NAME=":8080"