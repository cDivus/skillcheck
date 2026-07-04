# Stage 1: Build assets
FROM node:22 AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Production image
FROM dunglas/frankenphp:php8.4

WORKDIR /app

COPY . .
# Copy compiled assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD ["sh", "-c", "mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/app/public && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080"]