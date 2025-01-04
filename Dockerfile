FROM php:8.3.0-apache

# Apache ENVs
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid
ENV APACHE_SERVER_NAME localhost

RUN if command -v a2enmod >/dev/null 2>&1; then \
        a2enmod rewrite headers \
    ;fi

RUN docker-php-ext-install pdo pdo_mysql
# for mysqli if you want
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install PHP extensions
#RUN docker-php-ext-install -y soap
#RUN docker-php-ext-install gd
RUN docker-php-ext-install bcmath
#RUN docker-php-ext-install zip

RUN ( curl -sSLf https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - || echo 'return 1' ) | sh -s \
      gd xdebug

RUN apt-get clean \
  && apt-get update \
  && apt-get install -y \
  unzip \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libxml2-dev \
  libwebp-dev \
  libpng-dev \
  libzip-dev \
  libonig-dev \
  libcurl4-openssl-dev \
  && docker-php-ext-configure gd  --with-webp --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install xml dom curl mbstring intl gettext \
  && docker-php-ext-install zip \
  && pecl bundle -d /usr/src/php/ext apcu \
  && docker-php-ext-install /usr/src/php/ext/apcu \
  && docker-php-ext-install mysqli \
  && rm -rf /var/cache/apt/archives /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip


COPY php.ini ${PHP_INI_DIR}
RUN a2enmod rewrite

RUN apt-get update -y

# Copy files
COPY apache-conf /etc/apache2/apache2.conf

#RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
#RUN service apache2 restart

RUN apt-get update && apt-get install -y nodejs npm
#RUN npm install -y

# Expose Apache
EXPOSE 80
 
# Launch Apache
CMD ["/usr/sbin/apache2ctl", "-DFOREGROUND"]
