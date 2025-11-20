FROM php:8.2-cli

WORKDIR /srv/app

# Copiar proyecto
COPY . /srv/app

# Instalar Composer si hay composer.json y dependencias PHP
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi || true

# Puerto por defecto (Render asigna $PORT en tiempo de ejecución)
ENV PORT=10000
EXPOSE 10000

# Usar el servidor PHP embebido y permitir que el puerto sea configurable vía $PORT
CMD ["sh", "-lc", "php -S 0.0.0.0:${PORT:-10000} -t ."]
