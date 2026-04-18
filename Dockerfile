FROM php:8.3-fpm

ARG BUILD_ARGUMENT_ENV=prod
ENV APP_HOME /var/www/e_commerce
ARG HOST_UID=1000
ARG HOST_GID=1000
ENV USERNAME=www-data
ENV APP_ENV prod

# Install all the dependencies and enable PHP modules
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    postgresql-client \
    make \
    && docker-php-ext-configure pgsql \
    && docker-php-ext-install pgsql pdo_pgsql \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# create document root, fix permissions for www-data user and change owner to www-data
RUN mkdir -p $APP_HOME/public && \
    mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
    && usermod -o -u $HOST_UID $USERNAME -d /home/$USERNAME \
    && groupmod -o -g $HOST_GID $USERNAME \
    && chown -R ${USERNAME}:${USERNAME} $APP_HOME

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# set working directory
WORKDIR $APP_HOME

USER ${USERNAME}

# copy source files
COPY --chown=${USERNAME}:${USERNAME} ./backend $APP_HOME/

# ensure .env exists for Symfony (needed at composer install time)
RUN cp -n $APP_HOME/.env.prod $APP_HOME/.env 2>/dev/null || true

# install all PHP dependencies
RUN if [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then export APP_ENV=$BUILD_ARGUMENT_ENV && COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress --no-dev; \
    fi

# create cached config file .env.local.php in case is prod environment
RUN if [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then composer dump-env $BUILD_ARGUMENT_ENV; \ 
    fi

USER root

# copy entrypoint script
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ensure www-data can write JWT keys at runtime
RUN mkdir -p $APP_HOME/config/jwt && chown -R ${USERNAME}:${USERNAME} $APP_HOME/config/jwt

ENTRYPOINT ["entrypoint.sh"]
