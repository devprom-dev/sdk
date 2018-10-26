<?php

include "WikiTraceFormEmbedded.php";

class FieldWikiTrace extends FieldForm
{
 	private $object_it;
	private $trace;
	private $trace_object;
 	
 	function FieldWikiTrace( $object_it, $trace, $trace_object )
 	{
 		$this->object_it = $object_it;
 		$this->trace = $trace;
 		$this->trace_object = $trace_object;
 	}

 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function getTrace()
 	{
 		return $this->trace;
 	}

 	function setFilters( & $trace )
 	{
 		$trace->disableVpd();
 		
		$trace->addFilter( 
			new FilterAttributePredicate( 'SourcePage',  
				is_object($this->object_it) ? $this->object_it->getId() : 0 ) );
 	}
 	
 	function draw( $view = null )
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->drawBody();
		echo '</div>';
	}
	
	function getForm( & $trace ) {
		return new WikiTraceFormEmbedded( $trace, 'SourcePage', $this->getName() );
	}

    function render( $view )
	{
	    $this->drawBody( $view );
	}
	
	function drawBody( $view = null )
	{
		$this->setFilters( $this->getTrace() );
		
		$form = $this->getForm( $this->getTrace() );
 		$form->setTraceObject( $this->trace_object );
 			
		$object_it = $this->getObjectIt();
		if ( is_object($object_it) ) {
 			if ( !$this->getEditMode() ) {
 			    $form->setObjectIt( $object_it );
            }
 		}

 		$form->setReadonly( $this->readOnly() );
 		$form->setTabIndex( $this->getTabIndex() );
 			
 		echo '<div>';
	 		$form->draw( $view );
 		echo '</div>';
	}
}
 