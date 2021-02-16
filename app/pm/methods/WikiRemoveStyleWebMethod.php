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
 	
 	function getCaption() {
 		return text(1502);
 	}
 	
 	function getJSCall($parms = array()) {
 		return parent::getJSCall( array (
 				'object' => $this->object_it->getId(),
 				'class' => get_class($this->object_it->object)
 		));
 	}
 	
 	function hasAccess() {
 		return getFactory()->getAccessPolicy()->can_modify($this->object_it);
 	}
 	
 	function execute_request()
 	{
 		$object_it = $this->getObjectIt();
 		$object_it = $object_it->object->getRegistry()->Query(
 		    array (
 				new ParentTransitiveFilter($object_it->getId()),
                new FilterAttributePredicate('DocumentId', $object_it->get('DocumentId')),
 		    )
        );
 		
 		while( !$object_it->end() ) {
 			$object_it->object->getRegistry()->Store( $object_it, array (
 					'Content' => \TextUtils::getUnstyledHtml($object_it->getHtmlDecoded('Content')),
 					'UserField3' => null 
 			));
 			$object_it->moveNext();
 		}
 	}
}