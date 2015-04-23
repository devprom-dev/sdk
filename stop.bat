set DEV_ROOT=%~dp0\dev\
cd dev

call mysql\bin\mysqladmin -u root shutdown
taskkill /T /f /IM httpd.exe

