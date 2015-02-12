<?php
/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */

use Symfony\Component\HttpFoundation\Request;
use Devprom\Component\HttpKernel\ServiceDeskAppKernel;

include('../common.php');
include_once SERVER_ROOT_PATH."core/classes/PluginsFactory.php";

$plugins = new PluginsFactory();

$model_factory = new ModelFactoryExtended($plugins);

// --------------------

$kernel = new ServiceDeskAppKernel('prod', false);
$kernel->loadClassCache('sd-classes', '.php.cache');
$request = Request::createFromGlobals();
$request::enableHttpMethodParameterOverride();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
