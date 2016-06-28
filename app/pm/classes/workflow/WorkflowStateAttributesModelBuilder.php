<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class WorkflowStateAttributesModelBuilder extends ObjectModelBuilder 
{
	private $state_it = null;
	
	private $attributes = array();
	
	public function __construct( $state_it, $attributes = array() )
	{
		$this->state_it = $state_it;
		$this->attributes = $attributes;
	}
	
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof MetaobjectStatable ) return;
 	    if ( $object->getStateClassName() == '' ) return;
 	    
 	    if ( count($this->attributes) > 0 )
 	    {
     	    foreach( $object->getAttributes() as $attribute => $data )
	 	    {
	 	    	// skip custom attributes
	 	    	if ( $object->getAttributeOrigin($attribute) == ORIGIN_CUSTOM ) continue;
	 	    	if ( in_array($attribute, $this->attributes) ) continue;
	 	    	
	 	    	$object->setAttributeVisible($attribute, false);
	 	    	$object->setAttributeRequired($attribute, false);
	 	    }
	 	    
 	       	foreach( $this->attributes as $attribute ) {
				$object->setAttributeVisible($attribute, true);
			}
 	    }

		// show attributes visible on the first state
		$attribute_it = WorkflowScheme::Instance()->getStateAttributeIt($object);
		while( !$attribute_it->end() )
		{
			if ( $attribute_it->get('IsVisible') == 'Y' || $attribute_it->get('IsRequired') == 'Y' ) {
				$object->setAttributeVisible( $attribute_it->get('ReferenceName'), true );
				$object->setAttributeRequired(
					$attribute_it->get('ReferenceName'), $attribute_it->get('IsRequired') == 'Y'
				);
			}
			$attribute_it->moveNext();
		}

		// apply attributes settings for the given state
		$attribute_it = WorkflowScheme::Instance()->getStateAttributeIt($object, $this->state_it->get('ReferenceName'));
		while( !$attribute_it->end() )
		{
			$object->setAttributeRequired( 
					$attribute_it->get('ReferenceName'), $attribute_it->get('IsRequired') == 'Y' 
				);
			$object->setAttributeVisible(
					$attribute_it->get('ReferenceName'), 
					$attribute_it->get('IsVisible') == 'Y' || $attribute_it->get('IsRequired') == 'Y'
				);
			$attribute_it->moveNext();
		}
		$object->addAttribute('TransitionComment', 'WYSIWYG', text(1197), false, false);
	}
}