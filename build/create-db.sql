SET character_set_server=cp1251;
SET character_set_database=cp1251;
SET collation_database=cp1251_general_ci;

SET NAMES 'cp1251' COLLATE 'cp1251_general_ci';
SET CHARACTER SET cp1251;

create database devprom;
use devprom;

SET wait_timeout=600;
SET interactive_timeout=600;

create user devprom@localhost identified by 'devprom_pass';
grant all privileges on *.* to devprom@localhost with grant option;