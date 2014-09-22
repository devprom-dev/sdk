<?php

 if ( $_REQUEST['module'] == 'methods.php' )
 {
 	include ('methods.php');
 	
 	die();
 }

 include ('header.php');

 $module = $plugins->useModule( $_REQUEST['namespace'], 
 	'pm', $_REQUEST['module'] );
 
 if ( is_array($module) )
 {
 	$pageclass = $module['classname'];
 }

 if ( $pageclass != '' )
 {
 	 $page = new $pageclass;
 }
 else
 {
  	try 
 	{
 		Logger::getLogger('System')->error('There is no page for the given module: '.$_REQUEST['namespace'].'/'.$_REQUEST['module']);
 	}
 	catch( Exception $e)
 	{
 	}
 	
    exit(header('Location: '.getSession()->getApplicationUrl()));
 }

 $module = $model_factory->getObject('Module');
 
 $module_it = $module->getExact($_REQUEST['namespace'].'/'.$_REQUEST['module']);
 
 if ( !getFactory()->getAccessPolicy()->can_read($module_it) )
 {
 	exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
 }
 
 if ( !is_subclass_of($page, 'Page') )
 {
 	die();
 }

 if ( is_object($page) )
 {
 	$page->render();
 }
