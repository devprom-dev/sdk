<?php

include "FunctionTraceInverseFormEmbedded.php";
 
class FieldFunctionTrace extends FieldForm
{
 	var $object_it, $trace, $writable;
 	
 	function __construct( $object_it, $trace, $writable = true )
 	{
 		$this->object_it = $object_it;
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
	
 	function setFilters( & $trace )
 	{
		$trace->addFilter( 
			new FilterAttributePredicate( 'Feature', 
				is_object($this->object_it) ? $this->object_it->getId() : 0 ) );
 	}
 	
 	function getForm( & $trace )
	{
		return new ObjectTraceFormEmbedded( $trace, 'Feature', $this->getName() );
	}
	
 	function render( $view )
	{
	    $this->drawBody( $view );    
	}
	
	function drawBody( $view = null )
	{
		$this->setFilters( $this->trace );
		
 		$form = $this->getForm( $this->trace );
 		
 		$form->setTraceObject( getFactory()->getObject(
 			$this->trace->getObjectClass()) );
 			
		$object_it = $this->getObjectIt();
 		if ( is_object($object_it) ) {
 			if ( !$this->getEditMode() ) {
 			    $form->setObjectIt( $object_it );
            }
 		}

 		$form->setReadonly( $this->readOnly() );
 			
 		echo '<div>';
	 		$form->draw( $view );
 		echo '</div>';
	}
}