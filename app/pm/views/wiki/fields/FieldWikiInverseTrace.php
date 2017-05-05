<?php

include_once "FieldWikiTrace.php";
include "WikiTraceInverseFormEmbedded.php";

class FieldWikiInverseTrace extends FieldWikiTrace
{
 	function setFilters( & $trace )
 	{
 		$object_it = $this->getObjectIt();
 		
		$trace->addFilter( 
			new FilterAttributePredicate( 'TargetPage',  
				is_object($object_it) ? $object_it->getId() : 0 ) );
 	}

    function getValidator() {
        return new ModelValidatorEmbeddedForm($this->getName(), 'SourcePage');
    }

	function getForm( & $trace ) {
		return new WikiTraceInverseFormEmbedded( $trace, 'TargetPage', $this->getName() );
	}
}
