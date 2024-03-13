set ROOT=D:\docker\devprom-sdk
set DEV_ROOT=D:\docker\devprom-sdk\dev
D:
cd %ROOT%
pause
taskkill /T /f /IM %DEV_ROOT%\mysql\bin\mysqld.exe
rmdir /S /Q %DEV_ROOT%\mysql\data
rmdir /S /Q %DEV_ROOT%\apache\htdocs\cache
pause
%DEV_ROOT%\mysql\bin\mysqld --defaults-file=%DEV_ROOT%\mysql\my.ini --initialize-insecure
start %DEV_ROOT%\mysql\bin\mysqld --defaults-file=%DEV_ROOT%\mysql\my.ini --standalone --console
%DEV_ROOT%\mysql\bin\mysql --verbose --user=root --database=mysql --password=  -e "create user devprom@localhost identified by 'devprom_pass';"
%DEV_ROOT%\mysql\bin\mysql --verbose --user=root --database=mysql --password=  -e "grant all privileges on *.* to devprom@localhost with grant option;"
%DEV_ROOT%\mysql\bin\mysql --verbose --user=devprom --password=devprom_pass -e "source %DEV_ROOT%/db/devprom.sql"
%DEV_ROOT%\mysql\bin\mysql --verbose --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','{&quot;options&quot;:&quot;core&quot;}','WGgbMhy1E0/GMZPd+oUkzSZ49O8yHlapY5IlAqXHTdM9OLyzUBy65RZK8ofayD2lHbSyN0WkqWucxAfcCsfaDBBVjolgSW6ajuaEb56VWSwBYGKA8NrFu2nG6KteQ9HcCuehYZhi5//wW8QFNOk2GQUZyBnhjCN8U3VjLtOkUtw4JW+nNaiuU8e7rMBmIki01kL+dr+mc/AogxLmR3k4PgERb87eYQnlYx9g92SLvtq8wiuBcZ1n8as3DGcvNpvDz5lZGr/MpfRzVuWyTqEF395C6B2quCSbXMwQZxIKJq7lYBbyYamJ90UpzGSqvziwOv8TEOvVz/1X0SE0cZmhAQ==');"
pause
taskkill /T /f /IM %DEV_ROOT%\apache\httpd.exe
start %DEV_ROOT%\apache\httpd
pause

cd %ROOT%

cd %ROOT%\app\ext
call ..\..\dev\php\php composer/composer.phar self-update --2
del composer.lock
call ..\..\dev\php\php composer/composer.phar install
pause
cd %ROOT%\lib
call ..\dev\php\php composer/composer.phar self-update --2
del composer.lock
call ..\dev\php\php composer/composer.phar install

pause