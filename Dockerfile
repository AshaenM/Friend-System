FROM dunglas/frankenphp

RUN install-php-extensions mysqli pdo_mysql

COPY . /app/public

ENV SERVER_NAME=":8080"