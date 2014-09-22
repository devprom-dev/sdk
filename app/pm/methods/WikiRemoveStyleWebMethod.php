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
 	
 	function getJSCall()
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
 			$content = $object_it->getHtmlDecoded('Content');
 			
 			$content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);

 			$content = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $content);
 			
 			$content = preg_replace('/(<table[^>]+)/i', '$1 border="1"', $content);
 			
 			$content = preg_replace('/(<\/?span[^>]*>)/i', '', $content);

 			$content = preg_replace('/\xA0|&nbsp;/i', ' ', $content);
 			
 			$object_it->object->getRegistry()->Store( $object_it, array (
 					'Content' => $content,
 					'UserField3' => null 
 			));
 			
 			$object_it->moveNext();
 		}
 	}
}