<?php
/*
 * DEVPROM (http://www.devprom.net)
 * command.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 include('common.php');
 
 include(SERVER_ROOT_PATH.'/core/c_command.php');
 include('commands/c_createuser.php');
 include('commands/c_login.php');
 
 $class = $_REQUEST['class'];
 $page = $_REQUEST['page'];
 
 if ( preg_match('/^[a-zA-Z0-9]+$/im', $class) < 1 )
 {
 	unset($class);
 }
 
 if(!isset($class)) exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));

 include('commands/c_'.$class.'.php');

 $query = 'codename='.c.'&caption='.$_REQUEST['Caption'].
 	'&login='.$_REQUEST['Login'].'&email='.$_REQUEST['Email'];
 
 if ( class_exists($class) )
 {
 	$command = new $class;	
 }
 else
 {
 	$command = $plugins->getCommand( $_REQUEST['namespace'], $class );
 }
 
 $result = $command->execute();
 
 if ( $result < 0 )
 {
 	exit(header('Location: '.$page.(strpos($page, '?') === false ? '?' : '&' ).'result='.$result.'&class='.$class.'&'.$query));
 }

?>