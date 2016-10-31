<?php
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
 
