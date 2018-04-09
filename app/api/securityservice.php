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

 $server->service(EnvironmentSettings::getRawPostData());
 
 // Logins user using his credentials
 function login( $username, $password, $codename = "" )
 {
 	global $session, $soap;
 	
 	$session = new SOAPSession();
 	
 	$user_it = $session->getUserIt();
 	if ( $user_it->getId() < 1 ) {
		// try to login user by front page
		try {
			$url = EnvironmentSettings::getServerUrl().'/auth';
			$data = array (
				'login' => $username,
				'pass' => $password
			);
			$soap->logInfo($url);

			$curl = CurlBuilder::getCurl();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_AUTOREFERER, true);
			curl_setopt($curl, CURLOPT_COOKIESESSION, true);
			curl_setopt($curl, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), "cookie"));
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$result = curl_exec($curl);
			if ( $result === false ) throw new Exception(curl_error($curl));

			$authFailedMessage = "Authentication has been failed for ".$username;

			$info = curl_getinfo($curl);
			if ( $info['http_code'] != 200 ) throw new Exception($authFailedMessage . " with status code: ".$info['http_code']);

			$data = JsonWrapper::decode($result);

			if ( !is_array($data) ) throw new Exception($authFailedMessage . " with result: ".var_export($result, true));
			if ( $data['state'] != 'redirect' ) throw new Exception($authFailedMessage . " with result: ".var_export($data, true));

			curl_close($curl);
			$user_it = getFactory()->getObject('cms_User')->getByRef('LCASE(Login)', strtolower($username));
		}
		catch( \Exception $e ) {
			$soap->logError($e->getMessage());
			return array('Key' => '', 'Url' => '');
		}
 	}
 	if ( $user_it->getId() < 1 ) {
 	    return array('Key' => '', 'Url' => '');
 	}
 	
	$soap->logInfo(
	    str_replace('%3', $codename, 
			str_replace('%1', $user_it->getDisplayName(), "AUTH: userName = %1, project = %3")));
	
	$factory = new AuthenticationSOAPFactory();
	$factory->setUser( $user_it );
	$factory->login( $codename );
	
	if ( $codename != '' && $factory->getProject() < 1 ) {
	    return array('Key' => '', 'Url' => '');
	}
	else {
	    return array('Key' => $factory->getToken(), 'Url' => _getServerUrl().'/api/data');
	}
}
