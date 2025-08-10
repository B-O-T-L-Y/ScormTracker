FROM php:8.4-fpm-alpine

ARG user
ARG uid

# Install packages
RUN apk --no-cache update && apk add --no-cache \
    zip \
    libzip-dev \
    mysql-client \
    libpng-dev \
    oniguruma-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libmcrypt-dev \
    libxml2-dev \
    icu-dev \
    autoconf \
    g++ \
    make \
    supervisor

# Configure and install required PHP extensions
RUN docker-php-ext-configure zip && \
    docker-php-ext-install zip pdo_mysql opcache
RUN docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-install pcntl

# Install intl
RUN docker-php-ext-install intl

# Create a non-root user with specified UID and group for running the app securely
RUN addgroup -g $uid -S $user && \
    adduser -u $uid -S -G $user $user

# Copy Composer binary from official Composer image (assumes multi-stage build)
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy application code into the container with proper ownership
COPY --chown=$user:$user ./backend /var/www/html

# Copy custom PHP configuration (upload size, memory limit, etc.)
COPY ./_docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Create Supervisor-related directories and set correct ownership
RUN mkdir -p /var/log/supervisor /var/run/supervisor /var/run && \
    chown -R $user:$user /var/log/supervisor /var/run/supervisor /var/run

# Create an empty Supervisor log file and set proper permissions
RUN touch /var/log/supervisor/supervisord.log && \
    chown $user:$user /var/log/supervisor/supervisord.log

# Copy Supervisor configuration file
COPY --chown=$user:$user ./_docker/php/supervisord.conf /etc/supervisord.conf

# Copy custom entrypoint script and make it executable
COPY ./_docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set correct ownership and permissions for Laravel writable directories
#RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
#    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache && \
#    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the PHP-FPM port so other services (e.g. Nginx) can connect to it
EXPOSE 9000

# Set the entrypoint script to be run at container startup
ENTRYPOINT ["sh", "/usr/local/bin/docker-entrypoint.sh"]

# Run the container as the non-root user
USER $user