<?php

include dirname(__FILE__).'/../common.php';
include SERVER_ROOT_PATH.'co/classes/SessionBuilderCommon.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/SessionBuilderProject.php';

// allow OPTIONS to be requested from any domain (CORS support)
if ( $_SERVER['REQUEST_METHOD'] == 'OPTIONS' )
{
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: Devprom-Auth-Key,Content-Type');
	exit();
}
if ( $_REQUEST['project'] == '') {
    EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

$model_factory = new ModelFactoryExtended( PluginsFactory::Instance(), getCacheService() );
$session = SessionBuilderProject::Instance()->openSession(
    array (
        'project' => $_REQUEST['project']
    )
);

if ( $session->getUserIt()->getId() < 1 ) {
    \SessionBuilder::Instance()->close();
    EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /logoff?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

$redirect = '';
$project_it = $session->getProjectIt();

if ( !is_object($project_it) || $project_it->getId() < 1 ) {
	if ( in_array($_REQUEST['project'], array('all','my')) ) {
		$redirect = '/profile';
	} else {
		$redirect = '/404?redirect='.urlencode($_SERVER['REQUEST_URI']);
	}
}
if ( $redirect != '' ) {
	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: '.$redirect));
}
