<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include('common.php');

if ( $_REQUEST['style'] == '' ) $_REQUEST['style'] = 'rpc';
if ( $_REQUEST['use'] == '' ) $_REQUEST['use'] = 'encoded';

 // Create SOAP service
 $server = new soap_server();
 
 // configure SOAP service
 $server->setHTTPContentTypeCharset('UTF-8');
 $server->decodeUTF8( false );
 
 $webservice = 'TestService';
 $namespace = 'tns';
 $url = _getServerUrl().'/api/testservice'; 
 
 $server->configureWSDL($webservice, $namespace, $url, $soap->getStyle());
 $server->wsdl->schemaTargetNamespace = $url;

 // export complex types (classes)
 $classes = array(
	'testscenario',
	'testexecution',
	'environment',
	'testexecutionresult',
	'request',
	'attachment'
 );
 
 foreach ( $classes as $class )
 {
 	 $object = $model_factory->getObject($class);

	 $server->wsdl->addComplexType(
	    $class,
	    'complexType',
	    'struct',
	    'sequence',
	    '',
	    $soap->getAttributes( $object )
	 );

	 $server->wsdl->addComplexType(
	    $class.'Array',
	    'complexType',
	    'array',
	    'sequence',
	    'SOAP-ENC:Array',
	    array(),
	    array( array('ref'=>'SOAP-ENC:arrayType',
					 'wsdl:arrayType'=>$namespace.':'.$class.'[]') ),
	    $namespace.':'.$class
	 );
 }
 	
 $server->register('Create',
    array(
		'token' => 'xsd:string', 
		'object' => $namespace.':testscenario' 
		),          
    array('return' => $namespace.':testscenario'),
    $namespace, $namespace.'.Create', $soap->getStyle(), $soap->getUse(), ''
 ); 
 	
 $server->register('Find',
    array(
		'token' => 'xsd:string', 
		'object' => $namespace.':testscenario' 
		),          
    array('return' => $namespace.':testscenario'),
    $namespace, $namespace.'.Find', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $server->register('Append',
    array(
		'token' => 'xsd:string', 
		'parent' => $namespace.':testscenario', 
		'object' => $namespace.':testscenario' 
		),          
    array('return' => $namespace.':testscenario'),
    $namespace, $namespace.'.Append', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $server->register('Run',
    array(
		'token' => 'xsd:string', 
		'object' => $namespace.':testscenario',
		'version' => 'xsd:string',
		'environment' => $namespace.':environment'
		),          
    array(
		'return' => $namespace.':testexecution'
		),
    $namespace, $namespace.'.Run', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $server->register('ReportResult',
    array(
		'token' => 'xsd:string', 
		'execution' => $namespace.':testexecution',
		'test' => $namespace.':testscenario',
		'result' => $namespace.':testexecutionresult',
		'description' => 'xsd:string', 
		),          
    array(),
    $namespace, $namespace.'.ReportResult', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $server->register('ReportIssue',
    array(
		'token' => 'xsd:string', 
		'execution' => $namespace.':testexecution',
		'test' => $namespace.':testscenario',
		'issue' => $namespace.':request'
		),          
    array(
		'return' => $namespace.':request'
    	),
    $namespace, $namespace.'.ReportIssue', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $server->register('ReportFile',
    array(
		'token' => 'xsd:string', 
		'execution' => $namespace.':testexecution',
		'test' => $namespace.':testscenario',
		'file' => $namespace.':attachment'
		),          
    array(
    	),
    $namespace, $namespace.'.ReportFile', $soap->getStyle(), $soap->getUse(), ''
 ); 

 $server->register('GetResult',
    array(
		'token' => 'xsd:string', 
		'execution' => $namespace.':testexecution'
		),
    array(
		'return' => $namespace.':testexecutionresult'
		),
    $namespace, $namespace.'.GetResult', $soap->getStyle(), $soap->getUse(), ''
 ); 

 ob_start();
 $server->service(EnvironmentSettings::getRawPostData());
		
 $result = ob_get_contents();
 ob_end_clean();
		
 SoapService::logInfo("RESPONSE: ".$result);
 
 echo $result;

 //////////////////////////////////////////////////////////////////////////////////////
 function Create( $token, $parms )
 {
 	global $soap, $server, $model_factory;
 	
 	unset($parms['Content']);
 	
 	$result = $soap->find( $token, 'testscenario', $parms );
 	
 	if ( count($result) < 1 )
 	{
 		return $soap->add( $token, 'testscenario', $parms );
 	}
 	else
 	{
		return $result[0];
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 function Find( $token, $parms )
 {
 	global $soap, $server, $model_factory;

 	unset($parms['Content']);
 	
 	$result = $soap->find( $token, 'testscenario', $parms );
 	
 	return $result[0];
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 function Append( $token, $parent_parms, $test_parms )
 {
 	global $soap, $server, $model_factory;

 	unset($parent_parms['Content']);
 	
 	$result = $soap->find( $token, 'testscenario', $parent_parms );
 	
 	if ( count($result) < 1 )
 	{
		$server->fault('', text(785));
 	}

	$test_parms['ParentPage'] = $result[0]['Id'];

 	$check = $soap->find( $token, 'testscenario', 
		array ( 'ParentPage' => $test_parms['ParentPage'], 
				'Caption' => $test_parms['Caption'] ) 
		);
 	
 	if ( count($check) < 1 )
 	{
	 	return $soap->add( $token, 'testscenario', $test_parms );
 	}
 	else
 	{
 		return $check[0];
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 function Run( $token, $test_parms, $version, $env_parms )
 {
 	global $soap, $server, $model_factory;

 	unset($test_parms['Content']);
 	
 	$test_result = $soap->find( $token, 'testscenario', $test_parms );
 	
 	if ( count($test_result) < 1 )
 	{
		$server->fault('', text(785));
 	}

	$scenario = $model_factory->getObject('TestScenario');
	$scenario_it = $scenario->getExact( $test_result[0]['Id'] );
	
	if ( $env_parms['Id'] != '' || $env_parms['Caption'] != '' )
	{
	 	$env_result = $soap->find( $token, 'environment', $env_parms );

		if ( $env_result[0]['Id'] > 0 )
		{
			$env = $model_factory->getObject('Environment');
			$env_it = $env->getExact( $env_result[0]['Id'] );
		}
	}

	$test = getFactory()->getObject('TestExecution');
	$test_it = $test->getExact($test->add_parms(
			array (
					'TestScenario' => is_object($scenario_it) ? $scenario_it->getId() : '',
					'Version' => $version,
					'Environment' => is_object($env_it) ? $env_it->getId() : ''
			)
	));
\Logger::getLogger('Commands')->error(var_export($test_it->getData(),true));
	return $soap->serializeToSoap( $test_it ); 
 }

 //////////////////////////////////////////////////////////////////////////////////////
 function ReportResult( $token, $execution_parms, $test_parms, $result_parms, $description = '' )
 {
 	global $soap, $server, $model_factory;

 	$exec_result = $soap->find( $token, 'testexecution', $execution_parms );
 	if ( count($exec_result) < 1 )
 	{
		$server->fault('', str_replace('%1', 'testexecution', text(788)) );
 	}

 	unset($test_parms['Content']);
 	
 	$test_result = $soap->find( $token, 'testscenario', $test_parms );
 	if ( count($test_result) < 1 )
 	{
	 	$test_result = array ( 
	 		$soap->add( $token, 'testscenario',
		 		array ( 'ParentPage' => $exec_result[0]['TestScenario'], 
						'Caption' => $test_parms['Caption'], 
						'Content' => $test_parms['Content'] ) 
				) );
 	}

 	$dict_result = $soap->find( $token, 'testexecutionresult', $result_parms );
 	if ( count($dict_result) < 1 )
 	{
		$server->fault('', str_replace('%1', 'testexecutionresult', text(788)) );
 	}

	$test = $model_factory->getObject('TestCaseExecution');
	$test_it = $test->getByRefArray(
		array( 'TestCase' => $test_result[0]['Id'],
			   'Test' => $exec_result[0]['Id'] ) 
		);

	if ( $test_it->count() < 1 )
	{
		$test_id = $test->add_parms( 
			array ( 'TestCase' => $test_result[0]['Id'], 
					'Test' => $exec_result[0]['Id'] ) );
					
		$test_it = $test->getExact( $test_id );
	}

	$test->modify_parms($test_it->getId(), 
			array( 
					'Result' => $dict_result[0]['Id'],
			   		'Description' => $test_it->utf8towin($description)
			)
	);
	
	$test_it = $test_it->getRef('Test');
	$test_it->updateResult();
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 function ReportIssue( $token, $execution_parms, $test_parms, $request_parms )
 {
 	global $soap, $server, $model_factory;

 	$exec_result = $soap->find( $token, 'testexecution', $execution_parms );
 	if ( count($exec_result) < 1 )
 	{
		$server->fault('', str_replace('%1', 'testexecution', text(788)) );
 	}

 	unset($test_parms['Content']);
 	
 	$test_result = $soap->find( $token, 'testscenario', $test_parms );

 	if ( count($test_result) < 1 )
 	{
		$server->fault('', str_replace('%1', 'testscenario', text(788)) );
 	}

	$test = $model_factory->getObject('TestCaseExecution');
	$test_it = $test->getByRefArray(
		array( 'TestCase' => $test_result[0]['Id'],
			   'Test' => $exec_result[0]['Id'] ) 
		);

	$type = $model_factory->getObject('pm_IssueType');
	$type_it = $type->getByRef('LCASE(ReferenceName)', 'bug');

	$request_parms['Type'] = $type_it->getId();
	$request_parms['TestCaseExecution'] = $test_it->getId();
	$request_parms['SubmittedVersion'] = $exec_result[0]['Version'];
	
	return $soap->add( $token, 'request', $request_parms );
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 function ReportFile( $token, $execution_parms, $test_parms, $file_parms )
 {
 	global $soap, $server, $model_factory;

 	$exec_result = $soap->find( $token, 'testexecution', $execution_parms );
 	if ( count($exec_result) < 1 )
 	{
		$server->fault('', str_replace('%1', 'testexecution', text(788)) );
 	}

 	$test_result = $soap->find( $token, 'testscenario', $test_parms );
 	if ( count($test_result) < 1 )
 	{
		$server->fault('', str_replace('%1', 'testscenario', text(788)) );
 	}

	$test = $model_factory->getObject('TestCaseExecution');
	$test_it = $test->getByRefArray(
		array( 'TestCase' => $test_result[0]['Id'],
			   'Test' => $exec_result[0]['Id'] ) 
		);

	$file_parms['ObjectClass'] = 'testcaseexecution';
	$file_parms['ObjectId'] = $test_it->getId();
	
	return $soap->add( $token, 'attachment', $file_parms );
 }
  
 //////////////////////////////////////////////////////////////////////////////////////
 function GetResult( $token, $execution_parms )
 {
 	global $soap, $server, $model_factory;

 	$exec_result = $soap->find( $token, 'testexecution', $execution_parms );
 	if ( count($exec_result) < 1 )
 	{
		$server->fault('', str_replace('%1', 'testexecution', text(788)) );
 	}

 	$result = $soap->find( $token, 
		'testexecutionresult', array( 'Id' => $exec_result[0]['Result'] ) );
		
	return $result[0];
 }

?>