set ROOT=%~dp0
set DEV_ROOT=%~dp0\dev\
cd dev

taskkill /T /f /IM mysqld.exe
rmdir /S /Q mysql\data
rmdir /S /Q apache\htdocs\cache

mysql\bin\mysqld --defaults-file=mysql\my.ini --initialize-insecure
start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console
mysql\bin\mysql --verbose --user=root --database=mysql --password=  -e "create user devprom@localhost identified by 'devprom_pass';"
mysql\bin\mysql --verbose --user=root --database=mysql --password=  -e "grant all privileges on *.* to devprom@localhost with grant option;"
mysql\bin\mysql --verbose --user=devprom --password=devprom_pass -e "source ../db/devprom.sql"
mysql\bin\mysql --verbose --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam',NULL,'073af8958ee59de0c67349d580b1def5');"

taskkill /T /f /IM httpd.exe
start apache\httpd

cd %ROOT%

cd %ROOT%\app\ext
call ..\..\dev\php\php composer/composer.phar self-update
del composer.lock
call ..\..\dev\php\php composer/composer.phar install

cd %ROOT%\lib
call ..\dev\php\php composer/composer.phar self-update
del composer.lock
call ..\dev\php\php composer/composer.phar install
