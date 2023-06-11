FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libonig-dev
#    build-essential \
#    curl \
#    git \
#    jpegoptim optipng pngquant gifsicle \
#    locales \
#    unzip \
#    vim \
#    zip
#

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

## Install PHP extensions
RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl \
    pdo_mysql

## Miscellaneous
#RUN docker-php-ext-install bcmath
#RUN docker-php-ext-install exif
#RUN pecl install redis && docker-php-ext-enable redis

#
## Install Cron
#RUN apt-get update && apt-get install -y cron && apt-get install mc -y
#RUN #echo "* * * * * root php /var/www/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
#RUN touch /var/log/cron.log
#
#CMD bash -c "cron && php-fpm"
