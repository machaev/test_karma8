FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    libonig-dev \
    htop \
    mc


# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

## Install PHP extensions
RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl \
    pdo_mysql

#
## Install Cron
#RUN apt-get update && apt-get install -y cron && apt-get install mc -y
#RUN #echo "* * * * * root php /var/www/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
#RUN touch /var/log/cron.log
#
#CMD bash -c "cron && php-fpm"
