<?php

include ('header.php');

$module = $plugins->useModule( $_REQUEST['namespace'],
 	'admin', $_REQUEST['module'] );

if ( is_array($module) )
{
	$pageclass = $module['classname'];
}

if ( $pageclass != '' )
{
	$page = new $pageclass;
	$page->render();
}
