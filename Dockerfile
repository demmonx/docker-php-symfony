FROM ubuntu:18.04

COPY entrypoint.sh /usr/local/bin/
VOLUME ["/install"]
VOLUME ["/var/www"]
ENV DEBIAN_FRONTEND noninteractive
ENV DEBCONF_NONINTERACTIVE_SEEN true

## preesed tzdata, update package index, upgrade packages and install needed software
RUN echo "tzdata tzdata/Areas select Europe" > /tmp/preseed.txt; \
    echo "tzdata tzdata/Zones/Europe select Paris" >> /tmp/preseed.txt; \
    debconf-set-selections /tmp/preseed.txt

RUN apt-get update && apt-get dist-upgrade -y

# Install php
RUN apt-get install -y \
      apache2 \
      php7.2 \
      php7.2-cli \
      php-apcu \
      php-xdebug \
      libapache2-mod-php7.2 \
      php7.2-gd \
      php7.2-json \
      php7.2-ldap \
      php7.2-mbstring \
      php7.2-mysql \
      php7.2-pgsql \
      php7.2-sqlite3 \
      php7.2-xml \
      php7.2-xsl \
      php7.2-zip \
      php7.2-soap \
      php7.2-curl \
      php7.2-opcache \
      postgresql-client \
      mariadb-client \ 
      composer \
      vim

RUN a2enmod rewrite
COPY apache-default /etc/apache2/sites-available/000-default.conf

RUN rm -rf /tmp/*

COPY symfony /symfony

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
