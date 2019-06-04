#!/bin/bash
set -e
if [[ ! -f /var/www/.env ]]; then
    cp -r /symfony/* /var/www
    cp -r /symfony/.env* /var/www
    chmod -R 775 /var/www
    chown -R www-data:www-data /var/www 
    /bin/bash /install/run.sh
fi 

PHP_ERROR_REPORTING=${PHP_ERROR_REPORTING:-"E_ALL"}
sed -ri "s/DOMAIN/$DOMAIN_NAME/g" /etc/apache2/sites-available/000-default.conf
sed -ri 's/^display_errors\s*=\s*Off/display_errors = On/g' /etc/php/7.2/apache2/php.ini
sed -ri 's/^display_errors\s*=\s*Off/display_errors = On/g' /etc/php/7.2/cli/php.ini
sed -ri "s/^error_reporting\s*=.*$//g" /etc/php/7.2/apache2/php.ini
sed -ri "s/^error_reporting\s*=.*$//g" /etc/php/7.2/cli/php.ini
echo "error_reporting = $PHP_ERROR_REPORTING" >> /etc/php/7.2/apache2/php.ini
echo "error_reporting = $PHP_ERROR_REPORTING" >> /etc/php/7.2/cli/php.ini

# Apache gets grumpy about PID files pre-existing, so remove them:
rm -f /var/run/apache2/apache2.pid
source /etc/apache2/envvars && exec /usr/sbin/apache2 -DFOREGROUND

