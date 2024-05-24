FROM php:8.1-apache

COPY ./virtual-hosts/api.conf /etc/apache2/sites-available/api.conf
COPY ./virtual-hosts/data-models.conf /etc/apache2/sites-available/data-models.conf

COPY ./src /var/www/api

RUN a2enmod rewrite && \
    a2dissite 000-default && \
    a2ensite api && \
    a2ensite data-models && \
    echo "Listen 8080" >> /etc/apache2/ports.conf && \
    echo "Listen 8000" >> /etc/apache2/ports.conf && \
    mkdir /var/www/data-models && \
    chown www-data:www-data -R /var/www/api && \
    chown www-data:www-data -R /var/www/data-models

EXPOSE 8080
EXPOSE 8000

CMD ["apache2-foreground"]
