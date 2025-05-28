FROM php:8.1-cli

# Instala las dependencias necesarias para sqlite
RUN apt-get update && apt-get install -y libsqlite3-dev

# Instala la extensión pdo_sqlite
RUN docker-php-ext-install pdo_sqlite

WORKDIR /app

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/public"]
