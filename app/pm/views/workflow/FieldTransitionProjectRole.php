<?php
include "FormTransitionProjectRoleEmbedded.php";

class FieldTransitionProjectRole extends FieldForm
{
 	var $object_it;
 	
 	function __construct( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function render( $view ) {
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject( 'TransitionRole' );
		if ( is_object($this->object_it) ) {
            $anchorIt = $anchor->getRegistry()->Query(
                array(
                    new TransitionRolePredicate($this->object_it->getId())
                )
            );
		}
		else {
            $anchorIt = $anchor->getEmptyIterator();
		}

		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
	 		$form = new FormTransitionProjectRoleEmbedded( $anchorIt, 'Transition' );
	 		$form->setReadonly( $this->readOnly() );
            $form->setTransitionIt($this->object_it);
	 		$form->draw( $view );
 		echo '</div>';
 	}
}