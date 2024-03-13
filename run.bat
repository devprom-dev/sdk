set ROOT=%~dp0
set DEV_ROOT=%~dp0\dev

cd dev

taskkill /T /f /IM %DEV_ROOT%\mysql\mysqld.exe
start %DEV_ROOT%\mysql\bin\mysqld --defaults-file=%DEV_ROOT%\mysql\my.ini --standalone --console

taskkill /T /f /IM %DEV_ROOT%\apache\httpd.exe
start %DEV_ROOT%\apache\httpd
