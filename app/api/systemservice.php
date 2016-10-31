<?php

 // PHPLOCKITOPT NOENCODE
 // PHPLOCKITOPT NOOBFUSCATE

include('common.php');

if ( $_REQUEST['style'] == '' ) $_REQUEST['style'] = 'rpc';
if ( $_REQUEST['use'] == '' ) $_REQUEST['use'] = 'encoded';

 // Create SOAP service
 $server = new soap_server();

 $server->setHTTPContentTypeCharset('UTF-8');
 $server->decodeUTF8( false );

 $webservice = 'SystemService';
 $namespace = 'tns';
 $url = _getServerUrl().'/api/systemservice'; 
 
 $server->configureWSDL($webservice, $namespace, $url, $soap->getStyle());
 $server->wsdl->schemaTargetNamespace = $url;

 $server->wsdl->addComplexType(
    'Plugin',
    'complexType',
    'struct',
    'sequence',
    '',
    array ( 'Namespace' => array('name' => 'Namespace', 'type' => 'xsd:string'),
    		'Licensed' => array('name' => 'Licensed', 'type' => 'xsd:string') )
 );
 
 $server->wsdl->addComplexType(
    'PluginArray',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array( array('ref'=>'SOAP-ENC:arrayType',
				 'wsdl:arrayType'=>$namespace.':Plugin[]') ),
    $namespace.':Plugin'
 );
 
 $soap->exportEntity( 'project', $namespace, $server );
 
 $server->register('getPlugins',
    array( 'token' => 'xsd:string' ),          
    array( 'return' => $namespace.':PluginArray' ),
    $namespace, $namespace.'.getPlugins', $soap->getStyle(), $soap->getUse(), 'Returns the list of registered plugins'
 ); 

 $server->register('getMyActiveProjects',
         array( 'token' => 'xsd:string' ),
         array( 'return' => $namespace.':projectArray' ),
         $namespace, $namespace.'.getMyProjects', $soap->getStyle(), $soap->getUse(), 'Returns the list of projects accessible to the user'
 );
 
 $server->service(EnvironmentSettings::getRawPostData());
 
 // Returns the list of registered plugins
 function getPlugins( $token )
 {
 	global $soap, $plugins;
 	
 	$soap->login( $token );
 	$items = $plugins->getNamespaces();
 	
 	$results = array();
 	foreach( $items as $plugin )
 	{
 		array_push( $results, 
 		 	array( 'Namespace' => $plugin->getNamespace(), 
 		 		   'Licensed' => $plugin->IsLicensed() ? "Y" : "N" )
 		 	);
 	}
 	return $results;
 }

 // Returns the list of projects accessible to the user
 function getMyActiveProjects( $token )
 {
     global $soap;
 
     $result = array();
     
     $soap->login( $token );
     
     $project_it = getFactory()->getObject('Project')->getRegistry()->Query(
     		array (
     				new ProjectStatePredicate('active'),
     				new ProjectParticipatePredicate()
     		)
     );

     while ( !$project_it->end() )
     {
         $result[] = $soap->serializeToSoap( $project_it );
         
         $project_it->moveNext();
     }
      
     return $result;
 }
 