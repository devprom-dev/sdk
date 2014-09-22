<?php

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] != '' ? rtrim($_SERVER['DOCUMENT_ROOT'],'/\\').'/' : dirname(__FILE__).'/');
 
include DOCUMENT_ROOT.'settings.php';

if ( !defined('SERVER_ROOT_PATH') ) define('SERVER_ROOT_PATH', dirname(__FILE__).'/');
 
if ( !defined('CACHE_PATH') ) define('CACHE_PATH', DOCUMENT_ROOT.'cache');
 
define( 'SHARED_DIRECTION_FWD', 'forward' );

define( 'SHARED_DIRECTION_BWD', 'backward' );

define( 'DUMMY_PROJECT_VPD', '-' );

define( 'MAINTENANCE_LOCK_NAME', 'maintenance' );

define( 'BACKGROUND_TASKS_LOCK_NAME', 'background' );
