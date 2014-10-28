<?php

class TaskResultDictionary extends FieldDictionary
{
 	var $type_it;
 	
 	function __construct ( $object, $task_type )
 	{
 		$this->type_it = $task_type > 0 ? $object->getExact($task_type) : $object->getEmptyIterator();
 		
 		parent::__construct( $object );
 	}
 	
 	function getOptions()
 	{
		$items = array();

		switch ( $this->type_it->getRef('ParentTaskType')->get('ReferenceName') )
		{
			case 'testing':
			    
				$result_it = getFactory()->getObject('pm_TestExecutionResult')->getAll();
				
				while ( !$result_it->end() )
				{
					array_push( $items, $result_it->getDisplayName() );
					
					$result_it->moveNext();
				}
				
				break;

			case 'support':
			    
				$items = array( translate(RESULT_RESOLVED), 
					translate(RESULT_FIXEDINDIRECTLY),
					translate(RESULT_CANTREPRODUCE),
					translate(RESULT_FUNCTIONSASDESIGNED) );
				
				break;
		}
		
		if ( count($items) < 1 )
		{
			$items = array( translate(RESULT_RESOLVED) );
		}

		$options = array();
		
		foreach ( $items as $item )
		{
		    $options[] = array (
		        'caption' => $item,
                'value' => $item         
		    );
		}

		return $options;
 	}
}