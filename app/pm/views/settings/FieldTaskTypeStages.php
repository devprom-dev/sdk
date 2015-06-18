<?php

include "FormTaskTypeStageEmbedded.php";

class FieldTaskTypeStages extends FieldForm
{
 	var $object_it;
 	
 	function FieldTaskTypeStages ( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function render( $view )
 	{
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
 		global $model_factory;

		$anchor = $model_factory->getObject( 'TaskTypeStage' );
		
		if ( is_object($this->object_it) )
		{
			$anchor->addFilter( new TaskTypeStageTaskTypePredicate($this->object_it->getId()) );
		}
		else
		{
			$anchor->addFilter( new TaskTypeStageTaskTypePredicate(0) );
		}

 		$form = new FormTaskTypeStageEmbedded( $anchor, 'TaskType' );

 		$form->setReadonly( $this->readOnly() );
 			
 		$form->draw($view);
 	}
}