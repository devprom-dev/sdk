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
		if ( is_object($this->object_it) && $this->object_it->getId() != '' ) {
            $anchorIt = $anchor->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('State', $this->object_it->get('ReferenceName')),
                    new FilterBaseVpdPredicate()
                )
            );
		}
		else {
            $anchorIt = $anchor->getEmptyIterator();
		}

 		$form = new FormStateTaskTypeEmbedded( $anchorIt, 'State' );
 		$form->setReadonly( $this->readOnly() );
 		$form->draw($view);
 	}
}