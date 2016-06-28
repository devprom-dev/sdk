<?php
include_once "WebMethod.php";

class GetAttributeWebMethod extends WebMethod
{
 	function execute_request()
 	{
 		global $_REQUEST, $model_factory;

		$object = $model_factory->getObject($_REQUEST['class']);
		 
		$object_it = $object->getExact(preg_split('/,/',$_REQUEST['object']));

		if ( $object_it->count() < 1 ) return;
		 
		while( !$object_it->end() )
		{
    		$result = html_entity_decode($object_it->get_native($_REQUEST['attr']), ENT_COMPAT | ENT_HTML401, APP_ENCODING);
    
            if ( $_REQUEST['converter'] == 'html2text' )
    	 	{
    	 		$totext = new \Html2Text\Html2Text( $result );
    	 		$result = $totext->getText();
    	 	}
    		
    	 	switch ( $_REQUEST['encoding'] )
    	 	{
    	 		case 'native':
    		 		break;
    
    	 		default:
    		 		$result = html_entity_decode($object_it->getHtmlValue($result), ENT_COMPAT | ENT_HTML401, APP_ENCODING);
    	 	}
    	 	
    	 	echo $result;
    	 	
    	 	$object_it->moveNext();
		}
 	}
}
