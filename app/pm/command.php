<?php

 $class = $_REQUEST['class'];
 if ( in_array('page', array_keys($_REQUEST) ))
 {
 	$page = $_REQUEST['page'];
 }

 if ( preg_match('/^[a-zA-Z0-9]+$/im', $class) < 1 )
 {
 	unset($class);
 }
 
 if ( !isset($class) )
 {
 	exit(header('Location: /404'));
 }

 include('common.php');
 include_once ('../core/c_command.php');

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
 	$command = $plugins->getCommand( $_REQUEST['namespace'], 'pm', $class );
 }

 $result = $command->execute();
 
 if ( $result < 0 )
 {
 	exit(header('Location: '.$page.(strpos($page, '?') === false ? '?' : '&' ).'result='.$result.'&class='.$class));
 }
 