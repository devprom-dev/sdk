<?php

include "FormTransitionResetFieldEmbedded.php";

class FieldTransitionResetField extends FieldForm
{
 	var $object_it;
 	var $state_it;
 	
 	function FieldTransitionResetField ( $object_it )
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

		$anchor = $model_factory->getObject( 'TransitionResetField' );
		
		$anchor->setStateIt( $this->state_it );
		
		if ( is_object($this->object_it) )
		{
			$anchor->addFilter( new FilterAttributePredicate('Transition', $this->object_it->getId()) );
		}
		else
		{
			$anchor->addFilter( new FilterAttributePredicate('VPD', '-') );
		}

 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
	 		$form = new FormTransitionResetFieldEmbedded( $anchor, 'Transition' );
	 		$form->setReadonly( $this->readOnly() );
	 			
	 		$form->draw( $view );
 		echo '</div>';
 	}
}