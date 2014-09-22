<?php

include "SpentTimeFormEmbedded.php";

class FieldSpentTime extends FieldForm
{
 	var $object_it;
 	
 	function FieldSpentTime( $object_it )
 	{
 		$this->object_it = $object_it;
 	}

 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_Activity');
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
 		return new SpentTimeFormEmbedded( $activity, $this->getAnchorField(), $this->getName() );
	}
	
	function render( & $view )
	{
	    $this->drawBody( $view );    
	}
	
	function drawBody( & $view = null )
	{
		global $model_factory;

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