set DEV_ROOT=%~dp0\dev
cd dev

start mysql\bin\mysqld --defaults-file=mysql\my.ini --standalone --console
start apache\httpd
