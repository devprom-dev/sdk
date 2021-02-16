<?php
include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";

class FieldTaskTrace extends FieldForm
{
 	var $task_it, $trace, $writable;
 	
 	function FieldTaskTrace( $task_it, $trace, $writable = true )
 	{
 		$this->object_it = $task_it;
 		$this->trace = $trace;
 		$this->writable = $writable;
 	}

 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function draw( $view = null )
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->drawBody();
		echo '</div>';
	}
	
  	function render( $view )
	{
	    $this->drawBody( $view );    
	}
	
 	function setFilters( & $trace )
 	{
		$trace->addFilter(
            is_object($this->object_it)
                ? new TaskTraceTaskPredicate($this->object_it->getId())
                : new FilterEmptyPredicate()
        );
 	}
 	
 	function getForm( & $trace )
	{
		$form = new ObjectTraceFormEmbedded( $trace, 'Task', $this->getName() );
        $form->setTraceObject( getFactory()->getObject($this->trace->getObjectClass()) );
        return $form;
	}
	
	function drawBody( $view = null )
	{
		$this->setFilters( $this->trace );
        $this->trace->disableVPD();
		
 		$form = $this->getForm($this->trace);
 		$form->setTraceFieldName( $this->getName() );

		$object_it = $this->getObjectIt();
 		if ( is_object($object_it) ) {
 			if ( !$this->getEditMode() ) {
 			    $form->setObjectIt( $object_it );
            }
 		}
 		
 		$form->setReadonly( $this->readOnly() );
 		$form->setTabIndex( $this->getTabIndex() );
		$form->draw( $view );
	}
}