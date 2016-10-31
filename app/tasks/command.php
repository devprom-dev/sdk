<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include '../common.php';
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
