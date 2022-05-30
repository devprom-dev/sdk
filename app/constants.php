<?php

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] != '' ? rtrim(realpath($_SERVER['DOCUMENT_ROOT']),'/\\').'/' : dirname(__FILE__).'/');
include DOCUMENT_ROOT.'settings.php';
define('SERVER_LOGS_PATH', dirname(DOCUMENT_ROOT) . '/logs');
if ( !defined('SERVER_ROOT_PATH') ) define('SERVER_ROOT_PATH', dirname(__FILE__).'/');
if ( !defined('CACHE_PATH') ) define('CACHE_PATH', dirname(DOCUMENT_ROOT).'/cache');
define( 'SHARED_DIRECTION_FWD', 'forward' );
define( 'SHARED_DIRECTION_BWD', 'backward' );
define( 'DUMMY_PROJECT_VPD', '-' );
define( 'MAINTENANCE_LOCK_NAME', 'maintenance' );
define( 'BACKGROUND_TASKS_LOCK_NAME', 'background' );
define( 'APP_CHARSET', 'utf8mb4'); //db level
define( 'APP_ENCODING', 'utf-8'); //app level (php,front)
define( 'EMAIL_MSG_ID_SEPARATOR', '-devpromalm-');
if ( date_default_timezone_get() != "UTC" ) date_default_timezone_set('UTC');

