<?php

include ('../common.php');
include ('nusoap/lib/nusoap.php');
include ('classes/SoapService.php');
include ('classes/SOAPSession.php');
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$plugins = new PluginsFactory();
 
$cache_service = getCacheService();

$model_factory = new ModelFactoryProject(
		$plugins, 
		$cache_service, 
		new APIAccessPolicy($cache_service), 
		$model_factory->getEventsManager()
);

// create session object
$session = new SOAPSession();

// create main soap service class
$soap = new SoapService;
