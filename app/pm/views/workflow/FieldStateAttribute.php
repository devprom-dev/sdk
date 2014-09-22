<?php

include "FormStateAttributeEmbedded.php";

class FieldStateAttribute extends FieldForm
{
 	private $object_it;

 	private $state_it;
 	
 	function __construct ( $object_it )
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
 		$state_class = $this->object_it instanceof IteratorBase 
 			? get_class($this->object_it->object) : get_class($this->object_it);
 		
		$anchor = getFactory()->getObject( 'StateAttribute' );
		
		$anchor->setAttributeType('State', 'REF_'.$state_class.'Id');
		
		if ( is_object($this->object_it) && $this->object_it instanceof IteratorBase )
		{
			$anchor->addFilter( new FilterAttributePredicate('State', $this->object_it->getId()) );
		}
		else
		{
			$anchor->addFilter( new FilterAttributePredicate('VPD', '-') );
		}
		
		$anchor->addFilter( new TransitionAttributeEntityAttributesPredicate() );

		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
	 		
			$form = new FormStateAttributeEmbedded( $anchor, 'State' );
	 		$form->setReadonly( $this->readOnly() );
	 		$form->draw( $view );
	 		
 		echo '</div>';
 	}
}