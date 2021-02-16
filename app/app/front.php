<?php
include (dirname(__FILE__).'/../common.php');

use Devprom\Component\HttpFoundation\DevpromRequest;
use Devprom\Component\HttpKernel\MainApplicationKernel;
use Symfony\Component\Routing\Exception;

$kernel = new MainApplicationKernel('prod', false);
$kernel->loadClassCache('classes', '.php.cache');

try
{
	$request = DevpromRequest::createFromGlobals();
	$response = $kernel->handle($request);
	if ( is_object($response) )
	{
		$response->send();
		$kernel->terminate($request, $response);
	}
}
catch( \LogicException $e )
{
    try {
        $message = $e->getMessage().PHP_EOL.$e->getTraceAsString();
        Logger::getLogger('System')->error($message);
    }
    catch( Exception $e ) {
        error_log($message);
    }
    header($_SERVER["SERVER_PROTOCOL"]." 302 Found");
    exit(header('Location: /logoff'));
}
catch( \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e )
{
	try {
		$message = $e->getMessage().PHP_EOL.$e->getTraceAsString();
		Logger::getLogger('System')->error($message);
	}
	catch( Exception $e ) {
		error_log($message);
	}
    header($_SERVER["SERVER_PROTOCOL"]." 302 Found");
    exit(header('Location: /logoff'));
}