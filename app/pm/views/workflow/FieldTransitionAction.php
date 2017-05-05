<?php
include_once "FormStateActionEmbedded.php";

class FieldTransitionAction extends FieldForm
{
 	var $object_it, $object;
 	
 	function __construct ( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function setObject( $object ) {
 		$this->object = $object;
 	}
 	
 	function render( $view ) {
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject('TransitionAction');
		if ( is_object($this->object_it) )
		{
			$anchor->addFilter( new FilterAttributePredicate('Transition', $this->object_it->getId()) );
			$entity = getFactory()->getObject($this->object_it->getRef('TargetState')->get('ObjectClass'));
		}
		else
		{
			$anchor->addFilter( new FilterAttributePredicate('Transition', 0) );
			$entity = $this->object;
		}

 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
 			$form = new FormStateActionEmbedded( $anchor, 'Transition' );
 			$form->setEntity( $entity );
	 		$form->setReadonly( $this->readOnly() );
	 		$form->draw( $view );
 		echo '</div>';
 	}
}