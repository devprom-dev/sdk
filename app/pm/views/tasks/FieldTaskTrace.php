<?php

include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";

class FieldTaskTrace extends FieldForm
{
 	var $task_it, $trace, $writable;
 	
 	function FieldTaskTrace( $task_it, $trace, $writable = true )
 	{
 		global $model_factory;
 		
 		$this->object_it = $task_it;
 		$this->trace = $trace;
 		$this->writable = $writable;
 	}

 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function draw()
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->drawBody();
		echo '</div>';
	}
	
  	function render( & $view )
	{
	    $this->drawBody( $view );    
	}
	
 	function setFilters( & $trace )
 	{
		$trace->addFilter( 
			new TaskTraceTaskPredicate( is_object($this->object_it) ? $this->object_it->getId() : 0 ) );
 	}
 	
 	function getForm( & $trace )
	{
		return new ObjectTraceFormEmbedded( $trace, 'Task' );
	}
	
	function drawBody( & $view = null )
	{
		global $model_factory;

		$this->setFilters( $this->trace );
		
 		$form = $this->getForm($this->trace);
 		
 		$form->setTraceFieldName( $this->getName() );
 		
 		$form->setTraceObject( $model_factory->getObject(
 			$this->trace->getObjectClass()) );
 			
		$object_it = $this->getObjectIt();
		
 		if ( is_object($object_it) )
 		{
 			if ( !$this->getEditMode() ) $form->setObjectIt( $object_it );
 		}
 		
 		$form->setReadonly( $this->readOnly() );
 			
 		$form->setTabIndex( $this->getTabIndex() );
 			
		$form->draw( $view );
	}
}