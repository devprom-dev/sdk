<?php

include (dirname(__FILE__).'/../common.php'); 
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

use Devprom\Component\HttpFoundation\DevpromRequest;
use Devprom\Component\HttpKernel\MainApplicationKernel;
use Symfony\Component\Routing\Exception;

$plugins = new PluginsFactory();

$model_factory = new ModelFactoryExtended($plugins);

$kernel = new MainApplicationKernel('prod', false);

$kernel->loadClassCache('classes', '.php.cache');

try
{
	$request = DevpromRequest::createFromGlobals();

	$response = $kernel->handle($request);
	
	$response->send();
	
	$kernel->terminate($request, $response);
}
catch( LogicException $e )
{
}
catch( Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e )
{
	header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently");
	
	exit(header('Location: /'));
}
