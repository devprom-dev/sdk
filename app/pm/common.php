<?php

include dirname(__FILE__).'/../common.php';
include SERVER_ROOT_PATH.'core/classes/SessionPortfolioAllProjects.php';
include SERVER_ROOT_PATH.'co/classes/SessionBuilderCommon.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/SessionBuilderProject.php';

$lock = new CacheLock();

// allow OPTIONS to be requested from any domain (CORS support)
$allowedCORS = $_SERVER['REQUEST_METHOD'] == 'OPTIONS';
if ( $allowedCORS ) {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: Devprom-Auth-Key,Content-Type');
	exit();
}

if ( $_REQUEST['project'] == '') {
    EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

$model_factory = new ModelFactoryExtended( PluginsFactory::Instance(), getCacheService() );

try {
    $session = SessionBuilderProject::Instance()->openSession(
        array (
            'project' => $_REQUEST['project']
        )
    );
    if ( $session->getUserIt()->getId() < 1 ) {
        \SessionBuilder::Instance()->close();
        EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /logoff?redirect='.urlencode($_SERVER['REQUEST_URI'])));
    }
}
catch( \Exception $e)
{
    if ( EnvironmentSettings::ajaxRequest() ) {
        exit();
    }
    $projectIt = SessionBuilderProject::Instance()->getUserProjectIt();
    if ( $projectIt->getId() != '' ) {
        exit(header('Location: /pm/'.$projectIt->get('CodeName')));
    }
    else {
        exit(header('Location: /projects/welcome'));
    }
}

$redirect = '';
$project_it = $session->getProjectIt();

if ( !is_object($project_it) || $project_it->getId() < 1 ) {
	$redirect = '/404?redirect='.urlencode($_SERVER['REQUEST_URI']);
}

if ( $redirect == '' && !$project_it->IsPortfolio() && !getFactory()->getAccessPolicy()->can_read($project_it) ) {
    $redirect = '/404?redirect='.urlencode($_SERVER['REQUEST_URI']);
}

if ( $redirect != '' ) {
	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: '.$redirect));
}
