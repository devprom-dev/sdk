<?php

include "FormTransitionProjectRoleEmbedded.php";

class FieldTransitionProjectRole extends FieldForm
{
 	var $object_it;
 	
 	function FieldTransitionProjectRole ( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function render( & $view )
 	{
 	    $this->draw( $view );
 	}
 	
 	function draw( & $view = null )
 	{
 		global $model_factory;

		$anchor = $model_factory->getObject( 'TransitionRole' );
		if ( is_object($this->object_it) )
		{
			$anchor->addFilter( new TransitionRolePredicate($this->object_it->getId()) );
		}
		else
		{
			$anchor->addFilter( new TransitionRolePredicate(0) );
		}

		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
	 		$form = new FormTransitionProjectRoleEmbedded( $anchor, 'Transition' );
	 		$form->setReadonly( $this->readOnly() );
	 			
	 		$form->draw( $view );
 		echo '</div>';
 	}
}