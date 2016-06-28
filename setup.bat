set ROOT=%~dp0
set DEV_ROOT=%~dp0\dev\
cd dev

call mysql\bin\mysqladmin -u root shutdown
start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console

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

cd %ROOT%\dev
chcp 1251
call mysql\bin\mysql -u root --password=  -e "create user devprom@localhost identified by 'devprom_pass'"
call mysql\bin\mysql -u root --password=  -e "grant all privileges on *.* to devprom@localhost with grant option"
call mysql\bin\mysql --user=devprom --password=devprom_pass -e "source ../db/devprom.sql"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','2','073af8958ee59de0c67349d580b1def5');"
