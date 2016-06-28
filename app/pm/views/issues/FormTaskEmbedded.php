<?php

include_once SERVER_ROOT_PATH."pm/views/tasks/FieldTaskTypeDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
 
class FormTaskEmbedded extends PMFormEmbedded
{
 	var $tasks_added;
 	
 	function FormTaskEmbedded($object = null, $anchor_field = null, $form_field = '')
 	{
 	    global $model_factory;
 	    
 	    if ( !is_object($object) ) $object = $model_factory->getObject('pm_Task');
 	    
 	    parent::__construct($object, $anchor_field, $form_field);
 	    
 	    $object = $this->getObject();
 	    
 	    $attributes = $object->getAttributes();
 	    
 	    foreach( array_keys($attributes) as $attribute )
 	    {
 	        if ( $object->IsAttributeRequired($attribute) && $object->getAttributeOrigin($attribute) == 'custom' ) continue;
 	        
 	        $object->setAttributeVisible( $attribute, false ); 
 	    }
 	    
 	    $object->setAttributeVisible( 'Caption', true );
 	    $object->setAttributeVisible( 'TaskType', true );
 	 	$object->setAttributeVisible( 'Planned', true );
 	 	$object->setAttributeVisible( 'Comments', true );
		$object->setAttributeVisible( 'Assignee', true );
 	}
 	
 	function process( $object_it )
 	{
 		$this->tasks_added = array();
 		
 		parent::process( $object_it );	

 		if ( $_REQUEST['dependencies'] != '' )
 		{
	 		$trace = getFactory()->getObject('TaskTraceTask');

	 		foreach( $this->tasks_added as $key => $task_id )
	 		{
	 			if ( $this->tasks_added[$key + 1] < 1 ) break;
	 			
	 			$parms = array( 
	 					'Task' => $this->tasks_added[$key + 1], 
	 					'ObjectId' => $task_id
	 			); 
	 			
	 			$trace_it = $trace->getByRefArray($parms);
	 			
	 			if ( $trace_it->getId() < 1 ) $trace->add_parms($parms);
	 		}
	 	}
 	}
 	
 	function processAdded( $object_it )
 	{ 
 		array_push( $this->tasks_added, $object_it->getId() );
 	}
 	
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			default:
 				return parent::IsAttributeVisible( $attribute );
 		}
 	}
 	
 	function IsAttributeRequired( $attribute )
 	{
 	 	switch ( $attribute )
 		{
 			case 'Release':
				return true;
 			default:
 				return parent::IsAttributeRequired( $attribute );
 		}
 	}
 	
  	function getDiscriminator()
 	{
 		global $model_factory, $_REQUEST;

 		$field = $this->getDiscriminatorField();
 		
 		$object_it = $this->getObjectIt();
 		
 		if ( is_object($object_it) )
 		{
 			$ref_it = $object_it->getRef($field);
 			
 			return $ref_it->get('ReferenceName');
 		}
 		elseif ( $_REQUEST[$field] > 0 )
 		{
 			$object = $this->getObject();
 			
 			$ref = $object->getAttributeObject($field);
 			
 			$ref_it = $ref->getExact($_REQUEST[$field]);
 			
 			return $ref_it->get('ReferenceName');
 		}
 	}

 	function getDiscriminatorField()
 	{
 		return 'TaskType';
 	}
 	
 	function getFieldValue( $attr )
 	{
 		switch( $attr )
 		{
 			case 'Release':
 				return '';
 				
 			default:
 				return parent::getFieldValue( $attr );
 		}
 	}
 	
	function createField( $attr )
	{
		switch ( $attr )
		{
			case 'TaskType':
				$tasktype = $this->getAttributeObject( $attr ); 
				$tasktype->addFilter( new FilterBaseVpdPredicate() );
				
				return new FieldTaskTypeDictionary( $tasktype );

			case 'Assignee':
				$object = $this->getAttributeObject( $attr );
				$object->addFilter( new UserWorkerPredicate() );

				return new FieldParticipantDictionary( $object );

			default:
				return parent::createField( $attr );			
		}
	}
}
