<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

 include('common.php');
 
 // Create SOAP service
 $server = new soap_server();

 $server->setHTTPContentTypeCharset('UTF-8');
 $server->decodeUTF8( false );

 $webservice = 'SecurityService';
 $namespace = 'tns';
 $url = _getServerUrl().'/api/securityservice'; 
 
 $server->configureWSDL($webservice, $namespace, $url, $soap->getStyle());
 $server->wsdl->schemaTargetNamespace = $url;

 $server->wsdl->addComplexType(
    'Token',
    'complexType',
    'struct',
    'sequence',
    '',
    array ( 'Key' => array('name' => 'Key', 'type' => 'xsd:string'),
    		'Url' => array('name' => 'Url', 'type' => 'xsd:string') )
 );
 
 $server->register('login',
    array(
		'username' => 'xsd:string',
    	'userpass' => 'xsd:string',
    	'project' => 'xsd:string' 
    	),          
    array('return' => $namespace.':Token'),
    $namespace, $namespace.'.Login', $soap->getStyle(), $soap->getUse(), 'Returns token to access other services'
 ); 

 $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
 $server->service($HTTP_RAW_POST_DATA);
 
 // Logins user using his credentials
 function login( $username, $password, $codename = "" )
 {
 	global $model_factory, $session, $soap;
 	
 	$session = new SOAPSession();
 	
 	$user_it = $session->getUserIt();

 	if ( $user_it->getId() < 1 )
 	{
    	$user = $model_factory->getObject('cms_User');
    	
    	$user_it = $user->getByRef('LCASE(Login)', strtolower($username));
    	
    	$hash = $user->getHashedPassword( $password );
    	
 		while ( !$user_it->end() )
    	{
    		if ( $user_it->get('Password') == $hash ) break;
    		
    		$user_it->moveNext();
    	}
 	}

 	if ( $user_it->getId() < 1 )
 	{
 	    return array('Key' => '', 'Url' => '');
 	}
 	
	$soap->logInfo(
	    str_replace('%3', $codename, 
    	    str_replace('%2', $hash, 
    	            str_replace('%1', $user_it->getDisplayName(), "AUTH: userName = %1, passwordHash = %2, project = %3"))));
	
	$factory = new AuthenticationSOAPFactory();
	
	$factory->setUser( $user_it );
	
	$factory->login( $codename );
	
	$project_id = $factory->getProject();
	
	if ( $codename != '' && $project_id < 1 )
	{
	    return array('Key' => '', 'Url' => '');
	}
	else
	{
	    return array('Key' => $factory->getToken(), 'Url' => _getServerUrl().'/api/data');
	}
}
