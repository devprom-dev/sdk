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
mysql\bin\mysql --verbose --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','{&quot;options&quot;:&quot;core&quot;}','WGgbMhy1E0/GMZPd+oUkzSZ49O8yHlapY5IlAqXHTdM9OLyzUBy65RZK8ofayD2lHbSyN0WkqWucxAfcCsfaDBBVjolgSW6ajuaEb56VWSwBYGKA8NrFu2nG6KteQ9HcCuehYZhi5//wW8QFNOk2GQUZyBnhjCN8U3VjLtOkUtw4JW+nNaiuU8e7rMBmIki01kL+dr+mc/AogxLmR3k4PgERb87eYQnlYx9g92SLvtq8wiuBcZ1n8as3DGcvNpvDz5lZGr/MpfRzVuWyTqEF395C6B2quCSbXMwQZxIKJq7lYBbyYamJ90UpzGSqvziwOv8TEOvVz/1X0SE0cZmhAQ==');"

taskkill /T /f /IM httpd.exe
taskkill /T /f /IM php-cgi.exe
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
