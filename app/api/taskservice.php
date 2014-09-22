<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

 include('common.php');
  
 // Create SOAP service
 $server = new soap_server();
 
 $server->setHTTPContentTypeCharset('UTF-8');
 $server->decodeUTF8( false );
 
 $webservice = 'TaskService';
 $namespace = 'tns';
 $url = _getServerUrl().'/api/'.strtolower($webservice); 
 
 $server->configureWSDL($webservice, $namespace, $url);
 $server->wsdl->schemaTargetNamespace = $url;

 $classes = array (
 	'request', 'task', 'tasktype', 'feature', 'release', 'iteration', 
 	'participant', 'project', 'user', 'tasktracebase', 
 	'participantrole', 'projectrole', 'taskstate', 'issuestate'
 );

 $soap->dataService( $classes, $namespace, $server );

?>