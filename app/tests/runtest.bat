set APP_ROOT=%~dp0/../ext
set TEST_ROOT=%~dp0

set PATH=%PATH%;%TEST_ROOT%../../dev/php

chcp 1251

call %APP_ROOT%\vendor\bin\phpunit.bat -c config.xml
