<?php

include dirname(__FILE__).'/../common.php';
include_once SERVER_ROOT_PATH.'core/classes/system/CacheLock.php';
include SERVER_ROOT_PATH.'co/classes/COSession.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/SessionPortfolio.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

// allow OPTIONS to be requested from any domain (CORS support)
if ( $_SERVER['REQUEST_METHOD'] == 'OPTIONS' )
{
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE');
	header('Access-Control-Allow-Headers: Devprom-Auth-Key,Content-Type');
	exit();
}

$model_factory = new ModelFactoryExtended( PluginsFactory::Instance(), getCacheService() );

$session = new COSession();
$state = new DeploymentState();

if ( !$state->IsReadyToBeUsed() ) exit(header('Location: /install'));
if ( $state->IsMaintained() ) exit(header('Location: /503'));

if ( $_REQUEST['project'] == '') {
 	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}
if ( getSession()->getUserIt()->getId() < 1 )
{
	getSession()->close();
	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /logoff?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

// resolve project code
$cache = new ProjectCache();
$cache_it = $cache->getByRef('CodeName', $_REQUEST['project']);

if ( $cache_it->getId() < 1 )
{
	// build portfolios
	$portfolio_it = getFactory()->getObject('Portfolio')->getAll();
	while( !$portfolio_it->end() )
	{
	     if ( !getFactory()->getAccessPolicy()->can_read($portfolio_it) ) {
	         $portfolio_it->moveNext(); continue;
	     }
	     if ( $_REQUEST['project'] == $portfolio_it->get('CodeName') ) {
			 if ( $portfolio_it->get('CodeName') == 'my' ) {
				 // when user participates only in one project, then redirect into it
                 $accessible = new ProjectAccessible();
				 $project_it = $accessible->getAll();
				 if ( $project_it->count() == 1 ) {
					 $session = new PMSession($project_it);
					 break;
				 }
			 }
			 // build session object for the given portfolio
	     	$session = $portfolio_it->getSession();
	     	break;
	     }
	     $portfolio_it->moveNext();
	}
	if ( $portfolio_it->getId() < 1 ) {
		// when there is no any portfolio available then redirect into the first project
		$accessible = new ProjectAccessible();
		$session = new PMSession($accessible->getAll());
	}
}
else {
	$session = new PMSession($cache_it);
}

// check if session has been configured
$redirect = '';
$project_it = getSession()->getProjectIt();
if ( !is_object($project_it) || $project_it->getId() < 1 ) {
	if ( in_array($_REQUEST['project'], array('all','my')) ) {
		$redirect = '/profile';
	} else {
		$redirect = '/404?redirect='.urlencode($_SERVER['REQUEST_URI']);
	}
}
else if ( !getFactory()->getAccessPolicy()->can_read($project_it) ) {
	$redirect = '/404?redirect='.urlencode($_SERVER['REQUEST_URI']);
}
if ( $redirect != '' ) {
	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: '.$redirect));
}
