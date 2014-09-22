set DEV_ROOT=%~dp0

rem update vendor

call %DEV_ROOT%php/php composer/composer.phar self-update

del composer.lock

call %DEV_ROOT%php/php composer/composer.phar install

