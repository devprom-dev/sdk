<?php
include "ComponentTraceInverseFormEmbedded.php";
 
class FieldComponentTrace extends FieldForm
{
 	var $object_it, $trace, $writable;
 	
 	function __construct( $object_it, $trace, $writable = true )
 	{
 		$this->object_it = $object_it;
 		$this->trace = $trace;
 		$this->writable = $writable;
 	}

 	function getObjectIt() {
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
			new FilterAttributePredicate( 'Component',
				is_object($this->object_it) ? $this->object_it->getId() : 0 ) );
 	}
 	
 	function getForm( & $trace )
	{
	    $form = new ObjectTraceFormEmbedded($trace, 'Component', $this->getName());
        $form->setTraceObject(getFactory()->getObject($this->trace->getObjectClass()));
		return $form;
	}
	
 	function render( $view ) {
	    $this->drawBody( $view );    
	}
	
	function drawBody( $view = null )
	{
		$this->setFilters( $this->trace );
        $this->trace->disableVPD();

 		$form = $this->getForm( $this->trace );
 		
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