<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once '../common.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";
include 'commands/TaskCommand.php';

use Devprom\Component\HttpKernel\MainApplicationKernel;

if ( !DeploymentState::IsInstalled() ) {
	die();
}

$model_factory = new ModelFactoryExtended(PluginsFactory::Instance());
$state = $model_factory->getObject('DeploymentState');
if ( !$state->IsActivated() ) {
	die();
}

$kernel = new MainApplicationKernel('prod', false);
$kernel->loadClassCache('classes', '.php.cache');
$kernel->boot();
