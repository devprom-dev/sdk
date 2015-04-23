set ROOT=%~dp0
set DEV_ROOT=%~dp0\dev\
cd dev

call mysql\bin\mysqladmin -u root shutdown
start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console

taskkill /T /f /IM httpd.exe
start apache\httpd

cd %ROOT%\app\ext
rmdir /S /Q vendor
call ..\..\dev\php\php composer/composer.phar self-update
del composer.lock
call ..\..\dev\php\php composer/composer.phar install

cd %ROOT%\lib
call ..\dev\php\php composer/composer.phar self-update
del composer.lock
call ..\dev\php\php composer/composer.phar install

cd %ROOT%
copy /b db\update.sql+build\update-middle.sql+version.txt+build\update-end.sql upgrade.sql
call dev\mysql\bin\mysql -u devprom --password=devprom_pass --database=devprom -e "source upgrade.sql"
del upgrade.sql
rmdir /S /Q dev\apache\htdocs\cache