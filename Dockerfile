# ==========================================
# Stage 1: Build React Frontend
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app/frontend

COPY electrician-app/frontend/package*.json ./
RUN npm install

COPY electrician-app/frontend ./
RUN npm run build

# ==========================================
# Stage 2: PHP / Laravel Production Server
# ==========================================
FROM php:8.2-cli-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    curl \
    git \
    libzip-dev \
    sqlite-dev \
    libpng-dev \
    oniguruma-dev \
    dos2unix \
    && docker-php-ext-install pdo pdo_sqlite mbstring bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel backend code
COPY electrician-app/backend ./

# Ensure .env exists from example
RUN cp .env.example .env

# Copy built React frontend assets into Laravel's public directory
COPY --from=frontend-builder /app/frontend/dist ./public/app

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create necessary directories and set permissions
RUN mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && touch database/database.sqlite \
    && chmod -R 777 storage bootstrap/cache database

# Copy and setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
