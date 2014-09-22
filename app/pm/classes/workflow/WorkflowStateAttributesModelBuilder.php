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
	 	    	
	 	    	$object->setAttributeVisible($attribute, false);
	 	    }
	 	    
 	       	foreach( $this->attributes as $attribute )
			{
				$object->setAttributeVisible($attribute, true);
			}
 	    }
 	    
		$attribute_it = getFactory()->getObject('StateAttribute')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('State', $this->state_it->getId() > 0 ? $this->state_it->getId() : '-1'),
				)
		);
		
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
    }
}