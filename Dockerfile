FROM php:8.3-apache

RUN apt update && \
    apt-get install -y nano && \
	apt-get install -y wget && \
    apt-get install -y libpng-dev && \
    apt-get install -y zlib1g-dev && \
    apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev &&\
	docker-php-ext-install gd && \
	docker-php-ext-install pdo pdo_mysql mysqli && \
	docker-php-ext-configure gd --with-freetype --with-jpeg

RUN wget https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar && \
	chmod +x pickle.phar && \
	mv pickle.phar /usr/local/bin/pickle && \
	touch /usr/local/etc/php/conf.d/redis.ini && \
	echo "extension=redis" >> /usr/local/etc/php/conf.d/redis.ini && \
	pickle install redis --defaults --php 8.3 --ini /usr/local/etc/php/conf.d/redis.ini

RUN a2enmod rewrite && a2enmod ssl && a2enmod headers