<?php
include_once "FieldTaskTrace.php";
include "TaskTraceInverseFormEmbedded.php";
 
class FieldTaskInverseTrace extends FieldTaskTrace
{
 	function setFilters( & $trace ) {
		$trace->addFilter( new TaskTraceObjectPredicate( 
				is_object($this->getObjectIt())
                    ? $this->getObjectIt()
                    : $trace->getEmptyIterator()
            ));
 	}
 	
 	function getForm( & $trace )
	{
		$form = new TaskTraceInverseFormEmbedded( $trace, 'ObjectId', $this->getName() );
        $form->setTraceObject(getFactory()->getObject('Task'));
        return $form;
	}
}
