<?php
include "FormTaskTypeStageEmbedded.php";

class FieldTaskTypeStages extends FieldForm
{
 	private $object_it;
 	
 	function __construct ( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function render( $view ) {
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject( 'TaskTypeStage' );
		
		if ( is_object($this->object_it) ) {
			$anchor->addFilter(new FilterAttributePredicate('TaskType', $this->object_it->getId()));
		}
		else {
			$anchor->addFilter(new FilterAttributePredicate('TaskType', 0));
		}

 		$form = new FormTaskTypeStageEmbedded( $anchor, 'TaskType' );
 		$form->setReadonly( $this->readOnly() );
 		$form->draw($view);
 	}
}