set DEV_ROOT=%~dp0/dev/

cd dev

start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console

start apache\httpd

rem update vendor

cd %DEV_ROOT%../app/ext/

call %DEV_ROOT%php/php composer/composer.phar self-update

del composer.lock

call %DEV_ROOT%php/php composer/composer.phar install

cd %DEV_ROOT%


