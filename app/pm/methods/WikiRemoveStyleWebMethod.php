<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class WikiRemoveStyleWebMethod extends WebMethod
{
 	var $object_it;
 	
 	function __construct( $object_it = null )
 	{
 		$this->object_it = $object_it;
 		
 		parent::__construct();
 	}
 	
 	function getCaption()
 	{
 		return text(1502);
 	}
 	
 	function getJSCall($parms = array())
 	{
 		return parent::getJSCall( array (
 				'object' => $this->object_it->getId(),
 				'class' => get_class($this->object_it->object)
 		));
 	}
 	
 	function hasAccess()
 	{
 		return getFactory()->getAccessPolicy()->can_modify($this->object_it);
 	}
 	
 	function execute_request()
 	{
 		$object_it = $this->getObjectIt();
 		
 		$object_it = $object_it->object->getRegistry()->Query( array (
 				new WikiRootTransitiveFilter($object_it->getId())
 		));
 		
 		while( !$object_it->end() )
 		{
 			$content = preg_replace(
				array (
					'/(<[^>]+)\s+style\s*=/i',
					'/(<[^>]+)\s+class\s*=/i',
					'/(<table[^>]+)/i',
					'/(<\/?span[^>]*>)/i',
					'/(&nbsp;|\xC2\xA0)/i'
				),
				array (
					'$1 was-style=',
					'$1 was-class=',
					'$1 border="1"',
					'',
					' '
				),
				$object_it->getHtmlDecoded('Content')
			);

 			//$content = preg_replace('/\xA0|&nbsp;/i', ' ', $content);
 			
 			$object_it->object->getRegistry()->Store( $object_it, array (
 					'Content' => $content,
 					'UserField3' => null 
 			));
 			
 			$object_it->moveNext();
 		}
 	}
}