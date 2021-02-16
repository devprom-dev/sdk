<?php

include dirname(__FILE__).'/../common.php';
include SERVER_ROOT_PATH.'core/classes/SessionPortfolioAllProjects.php';
include SERVER_ROOT_PATH.'co/classes/SessionBuilderCommon.php';
include SERVER_ROOT_PATH.'pm/classes/sessions/SessionBuilderProject.php';
include "app/MobileAppKernel.php";

use Devprom\Component\HttpFoundation\DevpromRequest;
use Symfony\Component\Routing\Exception;

$model_factory = new ModelFactoryExtended( PluginsFactory::Instance(), getCacheService() );

try {
    $session = SessionBuilderProject::Instance()->openSession(array('project' => 'all'));
    if ( $session->getUserIt()->getId() < 1 ) {
        \SessionBuilder::Instance()->close();
        exit(header('Location: /logoff'));
    }

    if ( $session->getProjectIt()->get('CodeName') != 'all' ) {
        if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED ) {
            $session = SessionBuilderProject::Instance()->openSession(array ('project' => 'my'));
            if ( $session->getProjectIt()->getId() < 1 ) {
                exit(header('Location: /logoff'));
            }
        }
        else {
            exit(header('Location: /logoff'));
        }
    }
}
catch( \Exception $e)
{
    \Logger::getLogger('System')->error($e->getTraceAsString());
    exit(header('Location: /logoff'));
}

$kernel = new MobileAppKernel('prod', false);
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
    try {
        $message = $e->getMessage().PHP_EOL.$e->getTraceAsString();
        Logger::getLogger('System')->error($message);
    }
    catch( Exception $e ) {
        error_log($message);
    }

    header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently");
    exit(header('Location: /'));
}
