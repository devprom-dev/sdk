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
    if ( $_REQUEST['uid'] != '' ) {
        $object_uid = new ObjectUid;
        $object_it = $object_uid->getObjectIt($_REQUEST['uid']);
        $info = $object_uid->getUIDInfo($object_it);
        if ( $info['project'] != '' && $info['project'] != $_REQUEST['project'] ) {
            $session = SessionBuilderProject::Instance()->openSession(
                array (
                    'project' => $info['project']
                )
            );
            if ( $session->getProjectIt()->getId() > 0 ) {
                exit(header('Location: ' . $info['url']));
            }
        }
    }
    if ( $session->getProjectIt()->getId() < 1 )
    {
        $projectsCount = getFactory()->getObject('Project')->getRegistry()->Count(
            array(
                new ProjectParticipatePredicate(),
                new ProjectStatePredicate('active')
            )
        );
        if ( $projectsCount < 1 ) {
            exit(header('Location: /projects/welcome'));
        }
        else {
            exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
        }
    }
}
catch( \Exception $e)
{
    if ( EnvironmentSettings::ajaxRequest() ) {
        exit();
    }
    exit(header('Location: /logoff?redirect='.urlencode($_SERVER['REQUEST_URI'])));
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
