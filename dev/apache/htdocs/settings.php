<?php

include dirname(__FILE__).'/settings_server.php';
include dirname(__FILE__).'/settings_const.php';

define('SERVER_FILES_PATH', SERVER_ROOT.'/files/');
define('SERVER_BACKUP_PATH', SERVER_ROOT.'\backup/');
define('SERVER_UPDATE_PATH', SERVER_ROOT.'\update/');
define('SERVER_CORPDB_PATH', SERVER_ROOT.'\mysql\data/');
define('SERVER_CORPMYSQL_PATH', SERVER_ROOT.'\mysql\bin\mysql.exe');
define('SERVER_TFS_CLI_PATH',SERVER_ROOT.'/tools/tee-clc');
define('SERVER_TEMP_PATH',SERVER_ROOT.'/tmp/');
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] != '' ? rtrim($_SERVER['DOCUMENT_ROOT'],'/\\').'/' : dirname(__FILE__).'/'); 
define('CACHE_PATH', DOCUMENT_ROOT.'cache');

$level = error_reporting();
        
if ( $level & E_NOTICE || $level & E_DEPRECATED )
{
	error_reporting(E_ERROR & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}
