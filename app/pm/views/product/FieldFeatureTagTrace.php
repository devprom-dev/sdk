<?php

include_once SERVER_ROOT_PATH."pm/views/tags/FieldTagTrace.php";
include_once SERVER_ROOT_PATH."pm/views/tags/CustomTagFormEmbedded.php";

class FieldFeatureTagTrace extends FieldTagTrace
{
 	function __construct( $anchor )
 	{
 		parent::__construct( $anchor, 'ObjectId' );
 	}
 	
	function getTagObject()
	{	
		global $model_factory;
		
		$tag = $model_factory->getObject('FeatureTag');
		
 		$anchor_it = $this->getAnchorIt();
 		
		$tag->addFilter( 
			new FilterAttributePredicate( 'ObjectId', 
				is_object($anchor_it) && $anchor_it->getId() > 0 ? $anchor_it->getId() : 0 ) );
				
		return $tag;
	}
	
    function getForm()
    {
        $form = new CustomTagFormEmbedded( $this->getTagObject(), $this->getField() );
        
        $form->setAnchorIt( $this->getAnchorIt() );
        
        return $form;
    }
}
