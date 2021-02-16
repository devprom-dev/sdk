set TEST_ROOT=%~dp0
set DEV_ROOT=%~dp0../../dev
set APP_ROOT=%~dp0/../ext
set COMPOSER_HOME=%APP_ROOT%/composer

rem update vendor

cd %APP_ROOT%

call %DEV_ROOT%/php/php %COMPOSER_HOME%/composer.phar self-update --2
call %DEV_ROOT%/php/php %COMPOSER_HOME%/composer.phar clearcache

del composer.lock

call %DEV_ROOT%/php/php -d memory_limit=-1 %COMPOSER_HOME%/composer.phar update --no-interaction

cd %TEST_ROOT%

set PATH=%TEST_ROOT%../../dev/php;%PATH%

php -v
php --ini

call %APP_ROOT%\vendor\bin\phpunit.bat -c config.xml