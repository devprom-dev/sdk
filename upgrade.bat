set ROOT=%~dp0
set DEV_ROOT=%~dp0\dev\

git remote add upstream https://github.com/devprom-dev/sdk.git
git fetch upstream
git checkout master
git merge -s recursive -X theirs upstream/master

cd dev
call mysql\bin\mysqladmin -u root shutdown
start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console

taskkill /T /f /IM httpd.exe
taskkill /T /f /IM php-cgi.exe
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

rmdir /S /Q dev\apache\htdocs\cache