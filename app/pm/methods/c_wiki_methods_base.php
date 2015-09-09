<?php

include_once SERVER_ROOT_PATH."core/methods/ExportWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class WikiWebMethod extends WebMethod
 {
 	function WikiWebMethod()
 	{
 		parent::WebMethod();
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class GetWikiContentWebMethod extends WebMethod
 {
 	function execute_request()
 	{
 		 $class = getFactory()->getClass($_REQUEST['class']);
 		 
 		 if ( !class_exists($class) ) throw new Exception('Unknown class name: '.$_REQUEST['class'] );
 		 
 		 if ( $_REQUEST['object'] < 1 ) throw new Exception('Object identifier should be given');
 		 
		 $object_it = getFactory()->getObject($class)->getExact($_REQUEST['object']);

		 if ( $object_it->getId() < 1 ) return;
		 
	 	 switch ( $_REQUEST['encoding'] )
	 	 {
	 		case 'native':
	 		    
 				$editor = WikiEditorBuilder::build($_REQUEST['editor']);
 				
 				$editor->setObjectIt( $object_it );

 				$parser = $editor->getEditorParser();
 				
	 			if ( is_object($parser) )
	 			{
 					$parser->setObjectIt( $object_it );
 					
	 				echo $parser->parse($object_it->getHtmlDecoded('Content'));
	 			}
	 			else
	 			{
		 			echo $object_it->getHtmlDecoded('Content');
	 			}
	 			
		 		break;

	 		default:
		 		echo $object_it->getHtml('Content');
	 	 }
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ToggleWikiNodeWebMethod extends WikiWebMethod
 {
 	function execute_request() 
 	{
 		global $_REQUEST;
 		$this->execute( $_REQUEST );
 	}
 	
 	function execute( $parms )
 	{
 		global $model_factory;

 		$wiki = $model_factory->getObject('WikiPage');
 		$wiki_it = $wiki->getExact( $parms['wiki'] );
 		
 		if ( $wiki_it->count() > 0 )
 		{
 			getSession()->getUserSettings()->setSettingsValue('WikiPageExp'.$wiki_it->getId(), 
 				$parms['state'] == 'true' ? 'Y' : 'N' );
 		}
 	}
 }
 
 /////////////////////////////////////////////////////////////////////////// 
 class WikiExportBaseWebMethod extends ExportWebMethod
 {
 	function getJSCall( $page_it = null, $class )
 	{
 		$objects = is_object($page_it) ? $page_it->getId() : '';
 		
 		$entity = is_object($page_it) ? 
 			(strtolower(get_class($page_it->object)) == 'metaobject' ? 
 				$page_it->object->getClassName() : get_class($page_it->object)) : ''; 
 		
 		return parent::getJSCall(
 			array( 'class' => $class,
 				   'objects' => $objects,
 				   'entity' => $entity ) 
 			);
 	}
 }
 
 /////////////////////////////////////////////////////////////////////////// 
 class WikiExportRtfWebMethod extends WikiExportBaseWebMethod
 {
 	function getCaption()
 	{
 		return text(1511);
 	}
 	
 	function getJSCall( $page_it = null, $class = 'WikiIteratorExportRtf' )
 	{
 		return parent::getJSCall( $page_it, $class );
 	}
 }

 /////////////////////////////////////////////////////////////////////////// 
 class WikiExportPdfWebMethod extends WikiExportBaseWebMethod
 {
 	function getCaption()
 	{
 		return translate('Экспорт в PDF');
 	}
 	
 	function getJSCall( $page_it = null, $class = 'WikiIteratorExportPdf' )
 	{
 		return parent::getJSCall( $page_it, $class );
 	}
 }
 
 /////////////////////////////////////////////////////////////////////////// 
 class WikiExportTemplatedWebMethod extends ExportWebMethod
 {
 	function getJSCall( $page_it = null, $template = '', $class )
 	{
 		$objects = is_object($page_it) ? $page_it->getId() : '';
 		
 		$entity = is_object($page_it) ? 
 			(strtolower(get_class($page_it->object)) == 'metaobject' ? 
 				$page_it->object->getClassName() : get_class($page_it->object)) : ''; 
 		
 		return parent::getJSCall(
 			array( 'class' => $class,
 				   'objects' => $objects,
 				   'entity' => $entity,
 				   'template' => $template ) 
 			);
 	}

 	function execute_request()
 	{
 		global $_REQUEST;
 		
 		parent::execute_request();
 		echo '&template='.SanitizeUrl::parseUrl($_REQUEST['template']);
 	}
 }
 
 /////////////////////////////////////////////////////////////////////////// 
 class WikiExportPreviewWebMethod extends WikiExportTemplatedWebMethod
 {
 	function getCaption()
 	{
 		return translate('Экспорт в HTML');
 	}
 	
 	function getJSCall( $page_it = null, $template = '', $class = 'WikiIteratorExportHtml' )
 	{
 		return parent::getJSCall( $page_it, $template, $class );
 	}
 }

 /////////////////////////////////////////////////////////////////////////// 
 class WikiExportCHMWebMethod extends WikiExportTemplatedWebMethod
 {
 	function getCaption()
 	{
 		return translate('Экспорт в CHM');
 	}
 	
 	function getJSCall( $page_it = null, $template = '', $class = 'WikiIteratorExportCHM' )
 	{
 		return parent::getJSCall( $page_it, $template, $class );
 	}
 }
