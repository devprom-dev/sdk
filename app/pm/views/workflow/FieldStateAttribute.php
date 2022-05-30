<?php
include "FormStateAttributeEmbedded.php";

class FieldStateAttribute extends FieldForm
{
 	private $object_it;
 	private $attributeObject;
 	
 	function __construct ( $object_it, $attributeObject ) {
        $this->object_it = $object_it;
        $this->attributeObject = $attributeObject;
 	}
 	
 	function render( $view )
 	{
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject( 'StateAttribute' );
        $anchor->setAttributeType('State', 'REF_'.$this->attributeObject->getStateClassName().'Id');
		
		if ( is_object($this->object_it) && $this->object_it instanceof IteratorBase ) {
            $anchorIt = $anchor->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('State', $this->object_it->getId()),
                    new TransitionAttributeEntityAttributesPredicate()
                )
            );
		}
		else {
            $anchorIt = $anchor->getEmptyIterator();
		}

		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$form = new FormStateAttributeEmbedded( $anchorIt, 'State' );
            $form->setAttributeObject($this->attributeObject);
	 		$form->setReadonly( $this->readOnly() );
	 		$form->draw( $view );
 		echo '</div>';
 	}
}