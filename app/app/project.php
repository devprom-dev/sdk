<?php

include (dirname(__FILE__).'/../pm/common.php'); 

use Devprom\Component\HttpFoundation\DevpromRequest;
use Devprom\Component\HttpKernel\PmApplicationKernel;
use Symfony\Component\Routing\Exception;

$kernel = new PmApplicationKernel('prod', false);

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
