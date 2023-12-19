FROM php:7.4.33-fpm
WORKDIR "/app"

RUN apt-get update && apt-get install -y zip unzip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug

RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY build/php.ini.overrides build/php-cli.ini.overrides /tmp/
COPY build/startup.sh build/debug.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/startup.sh  /usr/local/bin/debug.sh

RUN set -eux; \
    cat /tmp/php.ini.overrides >> $PHP_INI_DIR/php.ini ;\
    cat /tmp/php-cli.ini.overrides >> $PHP_INI_DIR/php-cli.ini; \
    rm /tmp/php.ini.overrides /tmp/php-cli.ini.overrides;

RUN echo 'PS1="(\${COMPOSE_PROJECT_NAME}):\w $ "' >> /root/.bashrc

ENTRYPOINT ["/usr/local/bin/startup.sh"]
CMD ["php-fpm"]