set ROOT=%~dp0
call run.bat
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
call mysql\bin\mysql --user=devprom --password=devprom_pass -e "source ../build/create-db.sql"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "source ../db/devprom.sql"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "source ../db/update.sql"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "call upgrade_db('3.4');"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','2','073af8958ee59de0c67349d580b1def5');"
