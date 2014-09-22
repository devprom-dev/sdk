<?php

include dirname(__FILE__).'/../app/bootstrap.php';
include SERVER_ROOT_PATH."co/views/Common.php";

$session = new COSession();

$user = $model_factory->getObject('cms_User');
 	
if ( $user->getRecordCount() < 1 )
{
 	exit(header('Location: /admin/users.php'));
}

//checkUserIsLogged();
 
function checkUserIsLogged()
{
 	global $_SERVER;
 	
 	if ( getSession()->getUserIt()->getId() < 1 )
 	{
 		$session = getSession();
 		$auth_factory = $session->getAuthenticationFactory();
 		
 		if ( $auth_factory->credentialsRequired() )
 		{
 			exit(header('Location: /login?redirect='.$_SERVER['REQUEST_URI']));
 		}
 		else
 		{
 			exit(header('Location: /404?redirect='.$_SERVER['REQUEST_URI']));
 		}
 	}
}
