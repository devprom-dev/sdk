#!/bin/bash
sudo cp sdk/deploy/codenvy/000-default.conf /etc/apache2/sites-available/
sudo cp sdk/deploy/codenvy/php-devprom.ini /etc/php5/apache2/conf.d/
sudo cp sdk/deploy/codenvy/mysql-devprom.cnf /etc/mysql/conf.d/
sudo service mysql restart
sudo service apache2 restart
mysql -u root --password=  -e "source sdk/build/create-db.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "source sdk/db/devprom.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "source sdk/db/update.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "call upgrade_db('3.5.35');"
mysql --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','2','073af8958ee59de0c67349d580b1def5');"
cp -R sdk/dev/apache/htdocs/* sdk/app/
cp sdk/deploy/codenvy/settings* sdk/app/
mkdir sdk/app/cache
mkdir sdk/files
mkdir sdk/backup
mkdir sdk/update
mkdir sdk/logs
cd sdk/app/ext
composer install
cd /projects/sdk
git config --global user.email "test@example.com"                                                                                                                                                 
git config --global user.name "developer"
git remote add upstream https://github.com/devprom-dev/sdk.git
sudo chown -R www-data:www-data /projects/sdk
sudo chmod -R 775 /projects/sdk
