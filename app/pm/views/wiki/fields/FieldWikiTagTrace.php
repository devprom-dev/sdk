<?php

include_once SERVER_ROOT_PATH.'pm/views/tags/FieldTagTrace.php';

class FieldWikiTagTrace extends FieldTagTrace
{
 	function __construct( $anchor )
 	{
 		parent::__construct( $anchor, 'Wiki' );
 	}
 	
	function getTagObject()
	{	
		$tag = getFactory()->getObject('WikiTag');
		
 		$anchor_it = $this->getAnchorIt();
 		
		$tag->addFilter( 
			new FilterAttributePredicate( 'Wiki', 
				is_object($anchor_it) ? $anchor_it->getId() : -1 ) );
				
		return $tag;
	}
}
