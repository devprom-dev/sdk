set TEST_ROOT=%~dp0
set APP_ROOT=%~dp0/../ext
set COMPOSER_HOME=%APP_ROOT%/composer

rem update vendor

cd %APP_ROOT%

call %TEST_ROOT%../../dev/php/php %COMPOSER_HOME%/composer.phar self-update

del composer.lock

call %TEST_ROOT%../../dev/php/php %COMPOSER_HOME%/composer.phar --dev update

cd %TEST_ROOT%

set PATH=%PATH%;%TEST_ROOT%../../dev/php

php -v

php --ini

call %APP_ROOT%\vendor\bin\phpunit.bat -c config.xml