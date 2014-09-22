<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

 include('common.php');
 include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorBuilder.php";

 // Create SOAP service
 $server = new soap_server();

 $server->setHTTPContentTypeCharset('UTF-8');
 $server->decodeUTF8( false );

 $webservice = 'WikiService';
 $namespace = 'tns';
 $url = _getServerUrl().'/api/wikiservice'; 
 
 $server->configureWSDL($webservice, $namespace, $url);
 $server->wsdl->schemaTargetNamespace = $url;

 // export common methods to wiki related entities
 $classes = array (
 	'project', 'requirement', 'testscenario', 'helppage', 'wikipagefile', 'wikipagetype'
 );
 
 $server->wsdl->addComplexType(
    'WikiNode',
    'complexType',
    'struct',
    'sequence',
    '',
    array ( 
    	'WikiId' => array('name' => 'WikiId', 'type' => 'xsd:int'),
    	'Caption' => array('name' => 'Caption', 'type' => 'xsd:string'),
    	'Content' => array('name' => 'Content', 'type' => 'xsd:base64Binary'),
    	'ParentWikiId' => array('name' => 'ParentWikiId', 'type' => 'xsd:int'),
    	'LevelNumber' => array('name' => 'LevelNumber', 'type' => 'xsd:int'),
    	'RecordVersion' => array('name' => 'RecordVersion', 'type' => 'xsd:int')
    )
 );
 
 $server->wsdl->addComplexType(
    'WikiNodeArray',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array( array('ref'=>'SOAP-ENC:arrayType',
				 'wsdl:arrayType'=>$namespace.':WikiNode[]') ),
    $namespace.':WikiNode'
 );
 
 $server->register('getWikiTreeHtml',
    array( 
    	'token' => 'xsd:string', 
    	'wiki' => 'xsd:int' 
    ),          
    array( 'return' => $namespace.':WikiNodeArray' ),
    $namespace, $namespace.'.getWikiTreeHtml', 'rpc', 'encoded', ''
 ); 

 $server->register('getWikiListHtml',
    array( 
    	'token' => 'xsd:string',
    	'nodeList' => $namespace.':WikiNodeArray'
    ),          
    array( 'return' => $namespace.':WikiNodeArray' ),
    $namespace, $namespace.'.getWikiListHtml', 'rpc', 'encoded', ''
 ); 
 
 $soap->dataService( $classes, $namespace, $server );
 
 // Returns wiki hierarchy transformed to Html
 function getWikiTreeHtml( $token, $wiki_id )
 {
 	global $soap, $model_factory;
 	
 	$soap->login( $token );

 	$wiki = $model_factory->getObject('WikiPage');
 	
 	$wiki_it = $wiki->getExact( $wiki_id ); 

 	if ( $wiki_it->getId() < 1 ) return array();
 	
	$editor = WikiEditorBuilder::build($wiki_it->get('ContentEditor'));
	
	$editor->setObjectIt( $wiki_it );
	
 	$parser = $editor->getHtmlParser();
 	
 	$parser->setObjectIt( $wiki_it );
 	
 	$parser->setRequiredExternalAccess();
 	
 	$parser->setExternalAccessUserAuthorization(false);
 	
 	$result = array();

 	array_push( $result, 
 		array (
 			'WikiId' => $wiki_it->getId(),
 			'ParentWikiId' => $soap->systemValueToSoap($wiki_it, 'ParentPage'),
 			'Caption' => $soap->systemValueToSoap($wiki_it, 'Caption'),
 			'Content' => base64_encode($parser->parse($wiki_it->getHtmlDecoded('Content'))),
 			'LevelNumber' => 0,
 			'RecordVersion' => $wiki_it->get('RecordVersion') == '' ? '0' : $wiki_it->get('RecordVersion')
 		)
 	);

 	_getWikiTreeHtml( $wiki_it, 1, $result );

 	return $result; 	
 }
 
 function _getWikiTreeHtml( $wiki_it, $level, & $result )
 {
 	global $soap;
 	
	$parent_id = $wiki_it->getId();
	$children_it = $wiki_it->getChildrenIt();
 	
	while ( $children_it->get('ParentPage') == $parent_id )
	{
	 	$editor = WikiEditorBuilder::build($children_it->get('ContentEditor'));

	 	$editor->setObjectIt( $children_it );
		
		$parser = $editor->getHtmlParser();
		
	 	$parser->setObjectIt( $children_it );
	 	
	 	$parser->setRequiredExternalAccess();
	 	
	 	$parser->setExternalAccessUserAuthorization();
		
 		array_push( $result, 
 			array (
 				'WikiId' => $children_it->getId(),
 				'ParentWikiId' => $soap->systemValueToSoap($children_it, 'ParentPage'),
 				'Caption' => $soap->systemValueToSoap($children_it, 'Caption'),
 				'Content' => base64_encode($parser->parse($children_it->getHtmlDecoded('Content'))),
 				'LevelNumber' => $level,
 				'RecordVersion' => $children_it->get('RecordVersion') == '' ? '0' : $children_it->get('RecordVersion')
 			)
 		);
 		
		$id = $children_it->getId();
		
 		_getWikiTreeHtml( $children_it, $level + 1, $result );
 		
		$children_it->moveTo('WikiPageId', $id);
 		$children_it->moveNext();
	}
 }

 // Returns wiki list transformed to Html
 function getWikiListHtml( $token, $nodes )
 {
 	global $soap, $model_factory;
 	
 	$soap->login( $token );
 	
 	$wiki = $model_factory->getObject('WikiPage');
 	$wiki_it = $wiki->getExact( $nodes[0]['WikiId'] ); 
 	
 	$ids = array();
 	foreach( $nodes as $node )
 	{
 		array_push( $ids, $node['WikiId'] );
 	}
 	
 	$wiki_it = $wiki_it->object->getExact( $ids );
 	
	$editor = WikiEditorBuilder::build($wiki_it->get('ContentEditor'));

 	$editor->setObjectIt( $wiki_it );
	
 	$parser = $editor->getHtmlParser();
 	
 	$parser->setObjectIt( $wiki_it );
 	
 	$parser->setRequiredExternalAccess();
 	
 	$parser->setExternalAccessUserAuthorization();
 	
 	$result = array();
 	while ( !$wiki_it->end() )
 	{
		$editor->setObjectIt( $wiki_it );
 		$parser->setObjectIt( $wiki_it );
 		
 		array_push( $result, 
 			array (
 				'WikiId' => $wiki_it->getId(),
 				'ParentWikiId' => $soap->systemValueToSoap($wiki_it, 'ParentPage'),
 				'Caption' => $soap->systemValueToSoap($wiki_it, 'Caption'),
 				'Content' => base64_encode($parser->parse($wiki_it->getHtmlDecoded('Content'))),
 				'LevelNumber' => 0,
 				'RecordVersion' => $wiki_it->get('RecordVersion') == '' ? '0' : $wiki_it->get('RecordVersion')
 			)
 		);
 		
		$wiki_it->moveNext();
 	}
 	
 	return $result;
}