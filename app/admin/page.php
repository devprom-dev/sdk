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
}
else
{
	exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

if ( is_object($page) )
{
	$page->render();
}
