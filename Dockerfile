FROM php:7.2-cli

MAINTAINER mail@martinmika.eu

RUN apt-get update && \
    apt-get install -y zlib1g-dev && \
	docker-php-ext-install zip

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
	&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
	&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
	&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
	&& rm -f /tmp/composer-setup.*

COPY composer.json /application/composer.json
COPY composer.lock /application/composer.lock

WORKDIR /application

RUN composer install

COPY . /application

WORKDIR /application/bin

CMD ["console"]