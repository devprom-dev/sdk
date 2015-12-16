<?php

 // PHPLOCKITOPT NOENCODE
 // PHPLOCKITOPT NOOBFUSCATE

 include('common.php');
  
 // Create SOAP service
 $server = new soap_server();
 
 // configure SOAP service
 $server->setHTTPContentTypeCharset('UTF-8');
 $server->decodeUTF8( false );
 
 $webservice = 'SupportService';
 $namespace = "tns";
 
 $url = _getServerUrl().'/api/supportservice'; 
 
 $server->configureWSDL($webservice, $namespace, $url, $soap->getStyle());
 $server->wsdl->schemaTargetNamespace = $url;

 // export complex types (classes)
 $classes = array(
	'request',
	'attachment',
	'environment',
	'requesttype',
	'priority',
	'feature'
 );
 foreach ( $classes as $class ) {
	 $soap->exportEntity($class, $namespace, $server);
 }
 	
 $server->register('RaiseIssue',
    array(
		'token' => 'xsd:string', 
		'issue' => $namespace.':request'
		),          
    array(
		'return' => $namespace.':request'
    	),
    $namespace, $namespace.'.RaiseIssue', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $server->register('AttachFile',
    array(
		'token' => 'xsd:string', 
		'issue' => $namespace.':request',
		'file' => $namespace.':attachment'
		),          
    array(
    	),
    $namespace, $namespace.'.AttachFile', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
 $server->service($HTTP_RAW_POST_DATA);

function RaiseIssue( $token, $request_parms )
{
 	global $soap;
	return $soap->add( $token, 'request', $request_parms );
}
 
function AttachFile( $token, $request_parms, $file_parms )
{
 	global $soap, $server;

 	$request_result = $soap->find( $token, 'request', $request_parms );
 	if ( count($request_result) < 1 ) {
		$server->fault('', str_replace('%1', 'request', text(788)) );
 	}

	$file_parms['ObjectClass'] = 'request';
	$file_parms['ObjectId'] = $request_result[0]['Id'];
	if ( $soap->getUse() == 'literal' ) {
		$file_parms['File'] = base64_decode($file_parms['File']);
	}

	return $soap->add( $token, 'attachment', $file_parms );
}
