<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include('common.php');
include('classes/model/DataModel.php');

if ( $_REQUEST['style'] == '' ) $_REQUEST['style'] = 'rpc';
if ( $_REQUEST['use'] == '' ) $_REQUEST['use'] = 'encoded';

// Create SOAP service
$server = new soap_server();

$server->setHTTPContentTypeCharset('UTF-8');
$server->decodeUTF8( false );

$webservice = 'DataService';
$namespace = 'tns';
$url = _getServerUrl().'/api/'.strtolower($webservice);

$server->configureWSDL($webservice, $namespace, $url, $soap->getStyle());
$server->wsdl->schemaTargetNamespace = $url;

$model = new DataModel();
$soap->dataService( $model->getAll()->fieldToArray('Caption'), $namespace, $server );
