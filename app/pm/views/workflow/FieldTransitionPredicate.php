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
 	
 	function render( & $view )
 	{
 	    $this->draw( $view );
 	}
 	
 	function draw( & $view = null )
 	{
 		global $model_factory;

		$anchor = $model_factory->getObject('TransitionPredicate');
		
		if ( is_object($this->object_it) )
		{
			$anchor->addFilter( new TransitionPredicateTransition($this->object_it->getId()) );
		}
		else
		{
			$anchor->addFilter( new TransitionPredicateTransition(0) );
		}
		
		$entity = $model_factory->getObject($this->state_it->get('ObjectClass'));

 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
	 		
 			$form = new FormTransitionPredicateEmbedded( $anchor, 'Transition' );
 			
 			$form->setEntity( $entity ); 
	 		$form->setReadonly( $this->readOnly() );
	 			
	 		$form->draw( $view );
 		echo '</div>';
 	}
}