<?php
include "FormTransitionAttributeEmbedded.php";

class FieldTransitionAttribute extends FieldForm
{
 	var $object_it;
 	var $state_it;
 	private $attributeObject = null;
 	
 	function __construct( $object_it, $attributeObject ) {
 		$this->object_it = $object_it;
 		$this->attributeObject = $attributeObject;
 	}
 	
 	function render( $view ) {
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject( 'StateAttribute' );
        $anchor->setAttributeType('State', 'REF_'.$this->attributeObject->getStateClassName().'Id');

        if ( is_object($this->object_it) ) {
            $anchorIt = $anchor->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('Transition', $this->object_it->getId()),
                    new TransitionAttributeEntityAttributesPredicate()
                )
            );
		}
		else {
            $anchorIt = $anchor->getEmptyIterator();
		}

		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
	 		$form = new FormStateAttributeEmbedded( $anchorIt, 'Transition' );
            $form->setAttributeObject($this->attributeObject);
	 		$form->setReadonly( $this->readOnly() );
            $form->draw( $view );
 		echo '</div>';
 	}
}