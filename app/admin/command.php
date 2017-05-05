<?php
if ( function_exists('opcache_reset') ) opcache_reset();

include dirname(__FILE__).'/../app/bootstrap.php';
include_once(dirname(__FILE__).'/../core/c_command.php');

$class = $_REQUEST['class'];
$page = $_REQUEST['page'];

if ( preg_match('/^[a-zA-Z0-9]+$/im', $class) < 1 )
{
	unset($class);
}

if ( !isset($class) )
{
	exit(header('Location: /admin'));
}

if ( file_exists(dirname(__FILE__).'/commands/c_'.$class.'.php') )
{
	include(dirname(__FILE__).'/commands/c_'.$class.'.php');
}

if ( class_exists($class) )
{
	$command = new $class;
}
else
{
	$command = $plugins->getCommand( $_REQUEST['namespace'], 'admin', $class );
}

$result = $command->execute();
