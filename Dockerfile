FROM php:8.3-cli
COPY vendor /usr/src/myapp/vendor
COPY task-1/bitrix24-php-sdk /usr/src/myapp/task-1/bitrix24-php-sdk
WORKDIR /usr/src/myapp/task-1/bitrix24-php-sdk
CMD [ "php", "./sdk-api-request.php" ]
