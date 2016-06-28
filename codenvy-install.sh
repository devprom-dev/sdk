#!/bin/bash
sudo apt-get -qq update
sudo apt-get -qq upgrade
sudo apt-get -y -q install anacron
sudo cp /projects/sdk/deploy/codenvy/000-default.conf /etc/apache2/sites-available/
sudo cp /projects/sdk/deploy/codenvy/php-devprom.ini /etc/php5/apache2/conf.d/
sudo cp /projects/sdk/deploy/codenvy/php-devprom.ini /etc/php5/cli/conf.d/
sudo cp /projects/sdk/deploy/codenvy/mysql-devprom.cnf /etc/mysql/conf.d/
sudo cp /projects/sdk/deploy/codenvy/cron-devprom /etc/cron.d/
sudo service mysql restart
mysql -u root --password=  -e "source /projects/sdk/build/create-db.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "source /projects/sdk/db/devprom.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "source /projects/sdk/db/update.sql"
mysql --user=devprom --password=devprom_pass --database=devprom -e "call upgrade_db('3.5.35');"
mysql --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','2','073af8958ee59de0c67349d580b1def5');"
cp -R /projects/sdk/dev/apache/htdocs/* sdk/app/
cp /projects/sdk/deploy/codenvy/settings* sdk/app/
mkdir /projects/sdk/app/cache
mkdir /projects/sdk/files
mkdir /projects/sdk/backup
mkdir /projects/sdk/update
mkdir /projects/sdk/logs
sudo composer self-update
cd /projects/sdk/app/ext
composer install
cd /projects/sdk/lib
composer install
cd /projects/sdk
sudo chown -R www-data:www-data /projects/sdk
sudo chmod -R 777 /projects/sdk
sudo rm -r /projects/sdk/app/cache
sudo rm -r /projects/sdk/app/conf/logger.xml
git config --global user.email "test@example.com"                                                                                                                                                 
git config --global user.name "developer"
git remote add upstream https://github.com/devprom-dev/sdk.git
git fetch upstream
git checkout master
git merge -s recursive -X theirs upstream/master
sudo service apache2 restart
sudo service cron restart
