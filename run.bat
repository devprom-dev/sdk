set DEV_ROOT=%~dp0\dev\
cd dev

call mysql\bin\mysqladmin -u root shutdown
start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console

taskkill /T /f /IM httpd.exe
start apache\httpd
