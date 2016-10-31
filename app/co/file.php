<?php

include('../common.php');
include SERVER_ROOT_PATH.'co/classes/SessionBuilderCommon.php';

$model_factory = new ModelFactoryExtended(PluginsFactory::Instance());
SessionBuilderCommon::Instance()->openSession();

require_once('../cms/c_file.php');
