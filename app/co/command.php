<?php

include dirname(__FILE__).'/../app/front.php';
include SERVER_ROOT_PATH.'/core/c_command.php';

$class = $_REQUEST['class'];
$page = $_REQUEST['page'];
 
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
 
if ( $result < 0 )
{
 	exit(header('Location: '.$page.(strpos($page, '?') === false ? '?' : '&' ).'result='.$result.'&class='.$class));
}

if ( $page != '' )
{
 	exit(header('Location: '.$page));
}
