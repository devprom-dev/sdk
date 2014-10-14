copy /b db\update.sql+build\update-middle.sql+version.txt+build\update-end.sql upgrade.sql
call dev\mysql\bin\mysql -u devprom --password=devprom_pass --database=devprom -e "source upgrade.sql"
del upgrade.sql
rmdir /S /Q dev\apache\htdocs\cache