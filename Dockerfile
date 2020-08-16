FROM php:7.2-cli

COPY . /usr/src/desafio-finnet

WORKDIR /usr/src/desafio-finnet

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
