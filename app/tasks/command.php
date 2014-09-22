<?php

include_once '../common.php';
include SERVER_ROOT_PATH.'co/classes/COSession.php';
include SERVER_ROOT_PATH.'core/c_command.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";
include 'commands/TaskCommand.php';

if ( !DeploymentState::IsInstalled() )
{
	die();
}

$plugins = new PluginsFactory();
 
$model_factory = new ModelFactoryExtended($plugins);

$state = $model_factory->getObject('DeploymentState');

if ( !$state->IsReadyToBeUsed() )
{
	die();
}

// create session object
$session = new COSession();

$class = $_REQUEST['class'];

$page = SanitizeUrl::parseSystemUrl($_REQUEST['redirect']);
 
if ( preg_match('/^[a-zA-Z0-9]+$/im', $class) < 1 )
{
 	unset($class);
}
 
if(!isset($class)) exit(header('Location: /404'));

$module = dirname(__FILE__).'/commands/c_'.$class.'.php';
if ( file_exists($module) )
{ 
 	include( $module );
}

if ( class_exists($class) )
{
 	$command = new $class;	
}
else
{
 	$command = $plugins->getCommand( $_REQUEST['namespace'], 'co', $class );
}
 
$result = $command->execute();
 
if ( $page != '' )
{
 	exit(header('Location: '.$page));
}
