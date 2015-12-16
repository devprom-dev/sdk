<?php

include_once dirname(__FILE__).'/../common.php';
include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$model_factory = new ModelFactoryExtended(PluginsFactory::Instance());
$http_basic_factory = new AuthenticationHttpBasicFactory();

if ( $http_basic_factory->ready() )
{
	$session = new PMSession( $_REQUEST['project'].':', $http_basic_factory );

	$user_it = getSession()->getUserIt();
	
	if ( $user_it->getId() < 1 )
	{
		exit(header(' ', true, 403));
	} 
}

if ( !is_object($user_it) )
{
	$session = new PMSession( $_REQUEST['project'].':' );
	
	$user_it = getSession()->getUserIt();
	
	if ( $user_it->getId() < 1 )
	{
		$http_basic_factory->logon();
	}
}

require_once('../cms/c_file.php');
