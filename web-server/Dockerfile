#Recibimos Argumentos
ARG php_version

FROM php:${php_version}
LABEL  maintainer="ricurdinola@gmail.com"

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
		zip unzip libzip-dev \
		libmcrypt-dev \
        tzdata \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip

RUN cp /usr/share/zoneinfo/America/Argentina/Buenos_Aires /etc/localtime

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN chown www-data:www-data -R /var/www/html

#Copiamos de la imagen composer a la maquina local.
COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV NODE_VERSION=14.16.0

#RUN apt install -y curl
#RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
#ENV NVM_DIR=/root/.nvm
#RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
#RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
#RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
#ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
#RUN node --version
#RUN npm --version

COPY ./config/apache2/apache2.conf /etc/apache2/
COPY ./config/apache2/sites-available/000-default.conf /etc/apache2/sites-available/
COPY ./config/php/ /usr/local/etc/php

RUN a2enmod rewrite

#Seteamos el path con el directorio de composer
RUN echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc
	
EXPOSE 80