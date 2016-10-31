<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

 include('common.php');
 
 $server = new soap_server();

 $server->setHTTPContentTypeCharset('UTF-8');
 $server->decodeUTF8( false );

 $webservice = $_REQUEST['module'];
 $globalurn = 'urn:'.$webservice;
 
 $url = _getServerUrl().'/service/'.
 	$_REQUEST['namespace'].'/'.$_REQUEST['module']; 
 
 $server->configureWSDL($webservice, $globalurn, $url, $soap->getStyle());
 $server->wsdl->schemaTargetNamespace = $url;

 $module = $plugins->useModule( $_REQUEST['namespace'], 
 	'api', $_REQUEST['module'] );
 	
 $server->service(EnvironmentSettings::getRawPostData());
