set ROOT=%~dp0
call run.bat
cd %ROOT%

call lib/build.bat
cd dev
chcp 1251
call mysql\bin\mysql --user=devprom --password=devprom_pass -e "source ../db/devprom.sql"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "source ../db/update.sql"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "call upgrade_db('3.3');"
call mysql\bin\mysql --user=devprom --password=devprom_pass --database=devprom -e "insert into cms_License (LicenseType, LicenseValue, LicenseKey) values ('LicenseTeam','2','073af8958ee59de0c67349d580b1def5');"
