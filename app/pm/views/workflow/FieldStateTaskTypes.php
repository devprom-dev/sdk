<?php
include "FormStateTaskTypeEmbedded.php";

class FieldStateTaskTypes extends FieldForm
{
 	var $object_it;
 	
 	function __construct ( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function render( $view ) {
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject('TaskTypeState');
		
		if ( is_object($this->object_it) ) {
			$anchor->addFilter(new FilterAttributePredicate('State', $this->object_it->get('ReferenceName')));
		}
		else {
			$anchor->addFilter(new FilterAttributePredicate('State', '-'));
		}

 		$form = new FormStateTaskTypeEmbedded( $anchor, 'State' );
 		$form->setReadonly( $this->readOnly() );
 		$form->draw($view);
 	}
}