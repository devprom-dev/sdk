<?php

// locale
setlocale(LC_CTYPE, 'ru_RU.CP1251');

// default server time zone
date_default_timezone_set('UTC');

// environment
$_SERVER['APP_IID'] = INSTALLATION_UID;

// exception handling and logging
$handlers = array();

if ( file_exists(DOCUMENT_ROOT.'conf/logger.xml') )
{
	try
	{
	 	Logger::configure(DOCUMENT_ROOT.'conf/logger.xml');
	 	
		Logger::initialize();
	
	 	if ( !defined('SERVER_LOGS_PATH') )
	 	{
			foreach (Logger::getCurrentLoggers() as $logger) {
				foreach ($logger->getAllAppenders() as $appender) {
					$appender = $logger->getAppender($appender->getName());
					if (is_a($appender, 'LoggerAppenderRollingFile')) {
						define('SERVER_LOGS_PATH', dirname($appender->getFileName()));
					}
				}
	 		}
	 	}
	 	
	 	$handlers[] = new ExceptionHandlerListenerLogger();
	}
	catch(Exception $e)
	{
		error_log('Unable initialize logger: '.$e->getMessage());
	}
}

if ( !defined('SEND_BUG_REPORTS') || SEND_BUG_REPORTS )
{
	$handlers[] = new ExceptionHandlerListenerDevprom();
}

new core\classes\ExceptionHandler( $handlers );

// profiling
MetricsServer::Instance()->Start();
MetricsClient::Instance()->Start();

// database connection
if ( !DeploymentState::IsInstalled() )
{
     DALDummy::Instance()->Connect( '' );
}
else
{
     DALMySQL::Instance()->Connect(new MySQLConnectionInfo( DB_HOST, DB_NAME, DB_USER, DB_PASS ));
}

// model 		
$model_factory = $factory = new ModelFactory(
		new CacheEngine(), new AccessPolicy(new CacheEngine())
);

if ( DeploymentState::IsInstalled() )
{
	$_SERVER['APP_VERSION'] = $model_factory->getObject('cms_Update')->getLatest()->getDisplayName();
}
