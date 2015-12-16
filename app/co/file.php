<?php

include('../common.php');
include SERVER_ROOT_PATH.'co/classes/COSession.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$model_factory = new ModelFactoryExtended(PluginsFactory::Instance());
$session = new COSession();

require_once('../cms/c_file.php');
