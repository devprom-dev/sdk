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
 class ArchiveWikiWebMethod extends WikiWebMethod
 {
 	var $wiki_it;
 	
 	function ArchiveWikiWebMethod( $wiki_it = null )
 	{
 		$this->wiki_it = $wiki_it;
 		parent::WikiWebMethod();
 	}
 	
	function getDescription() 
	{
		if ( $this->wiki_it->IsArchived() )
		{
			return translate('Извлечь из архива');
		}
		else
		{
			return translate('Переместить в архив');
		}
	}
	
	function getCaption() 
	{
		if ( $this->wiki_it->IsArchived() )
		{
			return '<img border=0 src="/images/extract.png">';
		}
		else
		{
			return '<img border=0 src="/images/archive.png">';
		}
	}
	
	function getLinkStyle()
	{
		return 'modify_image';
	}

	function getUrl( $parms )
	{
		return parent::getUrl( 
			array( 'wiki_id' => $this->wiki_it->getId(),
				   'archive' => $this->wiki_it->IsArchived() ? '0' : '1' ));
	}
	
	function getJSCall()
	{
		return parent::getJSCall( 
			array( 'wiki_id' => $this->wiki_it->getId(),
				   'archive' => $this->wiki_it->IsArchived() ? '0' : '1' ));
	}

	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_modify($this->wiki_it);
	}
	
 	function execute_request() 
 	{
 		global $_REQUEST;
 		$this->execute( $_REQUEST );
 	}
	
 	function execute( $parms )
 	{
 		global $model_factory;
 		
 		$page = $model_factory->getObject('WikiPage');
 		$page_it = $page->getExact( $parms['wiki_id'] );

		if ( $parms['archive'] == 1 )
		{
			$page_it->Archive();
		}
		else
		{
			$page_it->Extract();
		}
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ArchiveWikiTextWebMethod extends ArchiveWikiWebMethod
 {
 	function getCaption()
 	{
 		return $this->getDescription();
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
