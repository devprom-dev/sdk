<?php

include dirname(__FILE__).'/../common.php';
include SERVER_ROOT_PATH.'co/classes/COSession.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/SessionPortfolio.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/SessionPortfolioMyProjects.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$plugins = new PluginsFactory();

$model_factory = new ModelFactoryExtended( $plugins, getCacheService() );

$session = new COSession();

$state = new DeploymentState();

if ( !$state->IsReadyToBeUsed() )
{
 	exit(header('Location: /install'));
}
 
if ( $state->IsMaintained() )
{
	exit(header('Location: /503'));
}
 
if ( $_REQUEST['project'] == '')
{
 	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

// resolve project code
$cache = new ProjectCache();

$cache_it = $cache->getByRef('CodeName', $_REQUEST['project']);

if ( $cache_it->getId() < 1 )
{
	// build portfolios
	$portfolio_it = getFactory()->getObject('Portfolio')->getAll();

	// build session object for the given portfolio
	while( !$portfolio_it->end() )
	{
	     if ( !getFactory()->getAccessPolicy()->can_read($portfolio_it) )
	     {
	         $portfolio_it->moveNext(); continue;
	     }
	     
	     if ( $_REQUEST['project'] == $portfolio_it->get('CodeName') )
	     {
	     	$session = $portfolio_it->getSession();

	     	break;
	     }

	     $portfolio_it->moveNext();
	}
}
else
{
	$session = new PMSession($cache_it);
}

// check if session has been configured
$project_it = getSession()->getProjectIt();

if ( !is_object($project_it) )
{
 	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

if ( $project_it->getId() < 1 )
{
 	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

if ( getSession()->getUserIt()->getId() < 1 )
{
	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /login?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}

if ( !getFactory()->getAccessPolicy()->can_read($project_it) )
{
	EnvironmentSettings::ajaxRequest() ? exit() : exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
}
