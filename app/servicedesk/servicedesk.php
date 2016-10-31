<?php
/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */

use Symfony\Component\HttpFoundation\Request;
use Devprom\Component\HttpKernel\ServiceDeskAppKernel;

include('../common.php');

$model_factory = new ModelFactoryExtended(PluginsFactory::Instance());

// --------------------

$kernel = new ServiceDeskAppKernel('prod', false);
$kernel->loadClassCache('sd-classes', '.php.cache');
$request = Request::createFromGlobals();
$request::enableHttpMethodParameterOverride();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
