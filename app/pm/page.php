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

 if ( $pageclass != '' ) {
 	 $page = new $pageclass;
 }
 else
 {
  	try {
 		Logger::getLogger('System')->error('There is no page for the given module: '.$_REQUEST['namespace'].'/'.$_REQUEST['module']);
 	}
 	catch( \Exception $e) {
 	}
    exit(header('Location: '.getSession()->getApplicationUrl()));
 }

if ( !is_object($page) ) {
    exit(header('Location: '.getSession()->getApplicationUrl()));
}
if ( !is_subclass_of($page, 'Page') ) {
    exit(header('Location: '.getSession()->getApplicationUrl()));
}

$page->render();
