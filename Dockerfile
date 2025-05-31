FROM php:8.1-apache

# Apache ENVs
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid
ENV APACHE_SERVER_NAME localhost

# Configurar PHP para suprimir warnings de compatibilidade
RUN { \
    echo 'error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE'; \
    echo 'display_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'memory_limit = 512M'; \
    echo 'date.timezone = America/Sao_Paulo'; \
    } > /usr/local/etc/php/conf.d/compatibility.ini

# Habilitar módulos Apache
RUN if command -v a2enmod >/dev/null 2>&1; then \
        a2enmod rewrite headers \
    ;fi

# Atualizar e instalar todas as dependências necessárias
RUN apt-get update && apt-get install -y \
    # Para GD
    libpng-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libxpm-dev \
    libfreetype6-dev \
    # Para mbstring (oniguruma)
    libonig-dev \
    # Dependências adicionais que podem ser úteis
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    # Ferramentas de build
    build-essential \
    autoconf \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PDO
RUN docker-php-ext-install pdo pdo_mysql

# Instalar MySQLi
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Instalar mbstring (agora com oniguruma disponível)
RUN docker-php-ext-install mbstring

# Configurar GD para PHP 8.1 (sintaxe atualizada)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --with-xpm

# Instalar GD
RUN docker-php-ext-install gd

# Instalar outras extensões úteis
RUN docker-php-ext-install \
    zip \
    intl \
    xml

# Copy files
COPY apache-conf /etc/apache2/apache2.conf

# Criar script de correção para Slim Framework 2
RUN echo '#!/bin/bash\n\
echo "Aplicando correções para Slim Framework 2 + PHP 8.1..."\n\
SLIM_ENV_FILE="/var/www/html/vendor/slim/slim/Slim/Environment.php"\n\
if [ -f "$SLIM_ENV_FILE" ]; then\n\
    echo "Corrigindo $SLIM_ENV_FILE..."\n\
    cp "$SLIM_ENV_FILE" "$SLIM_ENV_FILE.backup"\n\
    sed -i "s/public function offsetExists(\$offset)/public function offsetExists(\$offset): bool/" "$SLIM_ENV_FILE"\n\
    sed -i "s/public function offsetGet(\$offset)/public function offsetGet(\$offset): mixed/" "$SLIM_ENV_FILE"\n\
    sed -i "s/public function offsetSet(\$offset, \$value)/public function offsetSet(\$offset, \$value): void/" "$SLIM_ENV_FILE"\n\
    sed -i "s/public function offsetUnset(\$offset)/public function offsetUnset(\$offset): void/" "$SLIM_ENV_FILE"\n\
    echo "Correções aplicadas com sucesso!"\n\
fi\n\
SLIM_SET_FILE="/var/www/html/vendor/slim/slim/Slim/Helper/Set.php"\n\
if [ -f "$SLIM_SET_FILE" ]; then\n\
    echo "Corrigindo $SLIM_SET_FILE..."\n\
    cp "$SLIM_SET_FILE" "$SLIM_SET_FILE.backup"\n\
    sed -i "s/public function offsetExists(\$offset)/public function offsetExists(\$offset): bool/" "$SLIM_SET_FILE"\n\
    sed -i "s/public function offsetGet(\$offset)/public function offsetGet(\$offset): mixed/" "$SLIM_SET_FILE"\n\
    sed -i "s/public function offsetSet(\$offset, \$value)/public function offsetSet(\$offset, \$value): void/" "$SLIM_SET_FILE"\n\
    sed -i "s/public function offsetUnset(\$offset)/public function offsetUnset(\$offset): void/" "$SLIM_SET_FILE"\n\
    echo "Correções aplicadas em $SLIM_SET_FILE!"\n\
fi\n\
echo "Processo de correção concluído!"' > /usr/local/bin/fix-slim2.sh \
    && chmod +x /usr/local/bin/fix-slim2.sh

# Expose Apache
EXPOSE 80

# Launch Apache
CMD ["/usr/sbin/apache2ctl", "-DFOREGROUND"]