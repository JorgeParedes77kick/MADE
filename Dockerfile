FROM php:7.4-fpm-bullseye

RUN apt-get update && apt-get install -y curl unzip

RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -

RUN apt-get install -y nodejs

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir /app

WORKDIR /app

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

RUN npm install

RUN npm run build
