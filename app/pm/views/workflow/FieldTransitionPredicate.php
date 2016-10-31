<?php

include "FormTransitionPredicateEmbedded.php";

class FieldTransitionPredicate extends FieldForm
{
 	var $object_it, $state_it;
 	
 	function FieldTransitionPredicate ( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function setStateIt( $state_it )
 	{
 	    $this->state_it = $state_it;    
 	}
 	
 	function render( $view )
 	{
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject('TransitionPredicate');
		
		if ( is_object($this->object_it) )
		{
			$anchor->addFilter( new FilterAttributePredicate('Transition', $this->object_it->getId()) );
		}
		else
		{
			$anchor->addFilter( new FilterInPredicate(0) );
		}
		
 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
	 		
 			$form = new FormTransitionPredicateEmbedded( $anchor, 'Transition' );
 			
 			$form->setEntity(getFactory()->getObject($this->state_it->get('ObjectClass'))); 
	 		$form->setReadonly( $this->readOnly() );
            $form->setTransitionIt($this->object_it);
	 			
	 		$form->draw( $view );
 		echo '</div>';
 	}
}