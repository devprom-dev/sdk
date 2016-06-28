#!/bin/bash
sudo cp sdk/deploy/codenvy/000-default.conf /etc/apache2/sites-available/
sudo cp sdk/deploy/codenvy/php-devprom.ini /etc/php5/apache2/conf.d/
sudo cp sdk/deploy/codenvy/mysql-devprom.cnf /etc/mysql/conf.d/
sudo service mysql start
sudo service apache2 start
mysql -u root --password=  -e "source sdk/build/create-db.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "source sdk/db/devprom.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "source sdk/db/update.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "call upgrade_db('3.5.35');"
mysql --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','2','073af8958ee59de0c67349d580b1def5');"
cp -R sdk/dev/apache/htdocs/* sdk/app/
cp sdk/deploy/codenvy/settings* sdk/app/
cd sdk/app/ext
composer install