<?php
include_once SERVER_ROOT_PATH."pm/views/tags/FieldTagTrace.php";

class FieldRequestTagTrace extends FieldTagTrace
{
 	function __construct( $anchor ) {
 		parent::__construct( $anchor, 'Request' );
 	}
 	
	function getTagObject()
	{	
		$tag = getFactory()->getObject('pm_RequestTag');
		
 		$anchor_it = $this->getAnchorIt();
		$tag->addFilter(
			new FilterAttributePredicate( 'Request', 
				is_object($anchor_it) ? $anchor_it->getId() : 0 ) );
				
		return $tag;
	}
}
