<?php

include('../common.php');
include SERVER_ROOT_PATH.'co/classes/COSession.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$plugins = new PluginsFactory();

$model_factory = new ModelFactoryExtended($plugins);
 
$session = new COSession();

require_once('../cms/c_file.php');
