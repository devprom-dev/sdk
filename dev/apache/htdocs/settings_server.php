<?php
/*
define('DB_HOST', '?HOST');
define('DB_USER', '?USER');
define('DB_PASS', '?PASS');
define('DB_NAME', '?NAME');
*/

// MySQL server connection settings
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'devprom');
define('DB_PASS', 'devprom_pass');
define('DB_NAME', getenv('DB_NAME') );
//define('DB_NAME', 'devprom_test' );
	
// Server root folder
define('SERVER_ROOT', dirname(dirname(dirname(__FILE__))));
	
define('SERVER_ROOT', dirname(dirname(dirname(__FILE__))));

// MySQL encryption algorithm
define('MYSQL_ENCRYPTION_ALGORITHM', 'AES');

// Authorization options
define('AUTHORIZATION_FACTORY', 'AuthorizationCookiesFactory');

// Visualize performance metrics
define('METRICS_VISIBLE', true);
	
// Automatically send bug reports
define('SEND_BUG_REPORTS', false);

define('SERVER_NAME', 'devprom.local');
	
// External utilities
define('ZIP_HELP_COMMAND', 'zip --help');
define('ZIP_APPEND_COMMAND', 'zip -r %1 %2 %3');
define('UNZIP_HELP_COMMAND', 'unzip --help');
define('UNZIP_COMMAND', 'unzip %1');
define('MYSQLDUMP_HELP_COMMAND', 'mysqldump --help');
define('MYSQL_HELP_COMMAND', 'mysql --help');
define('MYSQL_INSTALL_COMMAND', 'mysql --host=%1 --user=%2 --password=%3 -e "source %4"');
define('MYSQL_BACKUP_COMMAND', 'mysqldump --set-charset --default-character-set=cp1251 --host=%1 --user=%2 --password=%3 --add-drop-table --force %4 > %5 ');
define('MYSQL_UPDATE_COMMAND', 'mysql --verbose --host=%1 --user=%2 --password=%3 --database=%4 -e "source %5" 2>&1 ');
define('MYSQL_APPLY_COMMAND', 'mysql --host=%1 --user=%2 --password=%3 --database=%4 -e "source %5" ');

define('SERVER_PORT', '80');

define('EMAIL_TRANSPORT', '1');

define('LANG_DATEFORMAT_', 'DateFormatEuropean');

define('LANG_DATEFORMAT_EN', 'DateFormatAmerican');

define('SERVER_INTERNAL_NAME', '');

define('EMAIL_SENDER_TYPE', 'admin');
	
//define('UI_EXTENSION', false);

//define('ALLOW_DEBUG', false);
//define('ALLOW_DEBUG', true);

//define('CACHE_ENGINE', 'CacheEngine');
//define('CACHE_ENGINE', 'CacheEngineVar');
//define('CACHE_ENGINE', 'CacheEngineMemcached');

define('METRICS_CLIENT', false);

//define('MATH_JAX_LIB_SRC', '/scripts/mathjax/MathJax.js?config=TeX-AMS_HTML');
