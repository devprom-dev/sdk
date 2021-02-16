<?php
include "FormTransitionPredicateEmbedded.php";

class FieldTransitionPredicate extends FieldForm
{
 	var $object_it, $state_it;
 	
 	function __construct( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function setStateIt( $state_it ) {
 	    $this->state_it = $state_it;    
 	}
 	
 	function render( $view ) {
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject('TransitionPredicate');
		if ( is_object($this->object_it) ) {
            $anchorIt = $anchor->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('Transition', $this->object_it->getId())
                )
            );
		}
		else {
            $anchorIt = $anchor->getEmptyIterator();
		}
		
 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
 			$form = new FormTransitionPredicateEmbedded( $anchorIt, 'Transition' );
 			$form->setEntity(getFactory()->getObject($this->state_it->get('ObjectClass')));
	 		$form->setReadonly( $this->readOnly() );
            $form->setTransitionIt($this->object_it);
	 		$form->draw( $view );
 		echo '</div>';
 	}
}