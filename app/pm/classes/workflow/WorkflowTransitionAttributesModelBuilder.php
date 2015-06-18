<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class WorkflowTransitionAttributesModelBuilder extends ObjectModelBuilder 
{
	private $transition_it = null;
	
	private $attributes = array();
	
	public function __construct( $transition_it, $attributes = array() )
	{
		$this->transition_it = $transition_it;
		$this->attributes = $attributes;
	}
	
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof MetaobjectStatable ) return;
 	    if ( $object->getStateClassName() == '' ) return;
 	    
 	    foreach( $object->getAttributes() as $attribute => $data )
 	    {
 	    	$object->setAttributeVisible($attribute, false);
 	    }

 	    if ( $this->transition_it->getId() == '' ) return;
 	    
        $attribute_it = getFactory()->getObject('StateAttribute')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('State', $this->transition_it->getRef('TargetState')->getId()),
				)
		);
		
		while( !$attribute_it->end() )
		{
			$object->setAttributeRequired( 
					$attribute_it->get('ReferenceName'), 
					$attribute_it->get('IsRequired') == 'Y'
				);
			
			$object->setAttributeVisible( 
					$attribute_it->get('ReferenceName'), 
					$attribute_it->get('IsVisible') == 'Y' || $attribute_it->get('IsRequired') == 'Y'
				);
			
			$attribute_it->moveNext();
		}
 	    
 	    $attribute_it = getFactory()->getObject('TransitionAttribute')->getRegistry()->Query(
 	    		array(
 	    				new FilterAttributePredicate('Transition', $this->transition_it->getId())
 	    		)
 	    );

		while( !$attribute_it->end() )
		{
			$object->setAttributeRequired( 
					$attribute_it->get('ReferenceName'), true
 	    	);

			$object->setAttributeVisible( 
					$attribute_it->get('ReferenceName'), true
				);
			
			$attribute_it->moveNext();
		}
		
		if ( $this->transition_it->get('IsReasonRequired') == 'Y' )
		{
			$object->setAttributeVisible( 'TransitionComment', true );
			$object->setAttributeRequired( 'TransitionComment', true );
		}
		
		foreach( $this->attributes as $attribute )
		{
			$object->setAttributeVisible($attribute, true);
		}
    }
}