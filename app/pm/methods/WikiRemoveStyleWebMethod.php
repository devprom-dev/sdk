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
            $unstyled = \TextUtils::getUnstyledHtml($object_it->getHtmlDecoded('Content'));
            if ( $unstyled != '' ) {
                 $object_it->object->modify_parms( $object_it->getId(), array (
                        'Content' => $unstyled,
 					    'UserField3' => null
 			        ));
            }
 			$object_it->moveNext();
 		}
 	}
}