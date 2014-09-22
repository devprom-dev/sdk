<?php

include '../common.php';
include 'classes/common/AdminAccessPolicy.php';
include 'classes/common/AdminSession.php';
include 'classes/model/ModelFactoryAdmin.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$plugins = new PluginsFactory();
 
$model_factory = new ModelFactoryAdmin(
		$plugins, 
		null, 
		new AdminAccessPolicy(getCacheService()), 
		$model_factory->getEventsManager()
);

// create session object
$session = new AdminSession();
