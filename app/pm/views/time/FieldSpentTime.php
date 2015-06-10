<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorEmbeddedForm.php";

include "SpentTimeFormEmbedded.php";
include "SpentTimeFormEmbeddedShort.php";

class FieldSpentTime extends FieldForm
{
 	var $object_it;
 	var $short_form = false;
 	
 	function FieldSpentTime( $object_it )
 	{
 		$this->object_it = $object_it;
 	}

 	function setShortMode( $short = true )
 	{
 		$this->short_form = $short;
 	}
 	
 	function getValidator()
 	{
 		return new ModelValidatorEmbeddedForm('Fact', 'Capacity');
 	}
 	
 	function getObject()
 	{
 		return getFactory()->getObject('pm_Activity');
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function getAnchorField()
 	{
 		return 'Task';
 	}
 	
 	function getLeftWorkAttribute()
 	{
 	    return '';
 	} 	
 	
	function & getForm( & $activity )
	{
		if ( $this->short_form )
		{
			return new SpentTimeFormEmbeddedShort( $activity, $this->getAnchorField(), $this->getName() );
		}
		else
		{
			return new SpentTimeFormEmbedded( $activity, $this->getAnchorField(), $this->getName() );
		}
	}
	
	function render( $view )
	{
	    $this->drawBody( $view );    
	}
	
	function drawBody( $view = null )
	{
		$activity = $this->getObject();
		
		$activity->addSort( new SortAttributeClause('ReportDate') );
		
 		$object_it = $this->getObjectIt();
		
 		$activity->setVpdContext($object_it);
		
 		$form = $this->getForm( $activity );
 		
 		$form->setLeftWorkAttribute( $this->getLeftWorkAttribute() );
		
 		if ( is_object($object_it) )
 		{
 			$form->setAnchorIt($object_it);
 			 
 			if ( !$this->getEditMode() ) $form->setObjectIt( $object_it );
 		}

 		$form->setReadonly( $this->readOnly() );
 			
 		$form->draw( $view );
	}
}