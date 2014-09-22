<?php

include('../common.php');
include SERVER_ROOT_PATH.'co/classes/COSession.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$plugins = new PluginsFactory();

$model_factory = new ModelFactoryExtended(
		$plugins, null, $model_factory->getAccessPolicy(), $model_factory->getEventsManager()
);
 
$session = new COSession();

$module = $plugins->useModule( $_REQUEST['namespace'], 'co', $_REQUEST['module'] );

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
