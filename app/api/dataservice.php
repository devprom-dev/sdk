<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include('common.php');
include('classes/model/DataModel.php');

// Create SOAP service
$server = new soap_server();

$server->setHTTPContentTypeCharset('UTF-8');
$server->decodeUTF8( false );

$webservice = 'DataService';
$namespace = 'tns';
$url = _getServerUrl().'/api/'.strtolower($webservice);

$server->configureWSDL($webservice, $namespace, $url);
$server->wsdl->schemaTargetNamespace = $url;

$model = new DataModel();

$soap->dataService( $model->getAll()->fieldToArray('Caption'), $namespace, $server );
