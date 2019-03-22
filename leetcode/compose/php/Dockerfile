FROM php:7.3-fpm
RUN apt-get update && apt-get install -y vim \
&& docker-php-ext-install pdo pdo_mysql
&& pecl install grpc
&& pecl install swoole
 
EXPOSE 9000