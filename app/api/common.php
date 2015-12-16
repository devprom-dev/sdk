<?php

include ('../common.php');
include ('nusoap/lib/nusoap.php');
include ('classes/SoapService.php');
include ('classes/SOAPSession.php');
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$cache_service = getCacheService();
$model_factory = new ModelFactoryProject(
		PluginsFactory::Instance(),
		$cache_service, 
		new APIAccessPolicy($cache_service)
);

// create session object
$session = new SOAPSession();

// create main soap service class
$soap = new SoapService;
