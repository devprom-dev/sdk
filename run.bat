set DEV_ROOT=%~dp0\dev\
cd dev

taskkill /T /f /IM mysqld.exe
start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console

taskkill /T /f /IM httpd.exe
taskkill /T /f /IM php-cgi.exe
start apache\httpd
