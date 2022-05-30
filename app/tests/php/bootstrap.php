<?php

define( 'SERVER_ROOT_PATH', dirname(__FILE__).'/../../');
define( 'DOCUMENT_ROOT', SERVER_ROOT_PATH);
define( 'CACHE_PATH', sys_get_temp_dir().'/cache');
define( 'APP_ENCODING', 'utf-8');
define( 'APP_CHARSET', 'utf8');

include SERVER_ROOT_PATH.'ext/vendor/autoload.php';

define('CACHE_ENGINE', 'CacheEngineVar');

// include common classes
include SERVER_ROOT_PATH.'classes.php';

// include installation factory
include SERVER_ROOT_PATH."admin/install/InstallationFactory.php";

// base test case class
include "DevpromTestCase.php";

// include symfony bundled code along with dependencies
include_once SERVER_ROOT_PATH . '/app/Devprom/Component/HttpKernel/ServiceDeskAppKernel.php';

// default server time zone
date_default_timezone_set('UTC');

// clear cache directory
FileSystem::rmdirr(CACHE_PATH);

include SERVER_ROOT_PATH.'tests/php/pm/DevpromDummyTestCase.php';
