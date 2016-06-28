#!/bin/bash
git fetch upstream
git checkout master
git merge -s recursive -X theirs upstream/master
sudo cp sdk/deploy/codenvy/000-default.conf /etc/apache2/sites-available/
sudo cp sdk/deploy/codenvy/php-devprom.ini /etc/php5/apache2/conf.d/
sudo cp sdk/deploy/codenvy/mysql-devprom.cnf /etc/mysql/conf.d/
sudo service mysql restart
sudo service apache2 restart
cd /projects/sdk/app/ext
composer install
cd /projects
rm -r sdk/app/cache