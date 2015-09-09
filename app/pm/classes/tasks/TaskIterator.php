<?php

class TaskIterator extends StatableIterator
{
 	function getDisplayName() 
 	{
 		$type_name = $this->getType();
 		return $type_name != "" ? $type_name.': '.$this->get('Caption') : parent::getDisplayName();
    }

 	function getType() 
 	{
 		if ( $this->get('TaskTypeDisplayName') != '' ) return $this->get('TaskTypeDisplayName');
		if ( $this->object->getAttributeType('TaskType') != '' ) {
			$task_type_it = $this->getRef('TaskType');
			if ( $task_type_it->getId() < 1 ) return '';
			return $task_type_it->getDisplayName();
		}
		return '';
	}
	
	function IsFinished()
	{
		return in_array($this->get('State'), $this->object->getTerminalStates());
	}
	
	function isFailed() 
	{
		$type_it = $this->getRef('TaskType');
		
		return $type_it->get('ReferenceName') == 'testing' && 
			$this->get('Result') == translate(RESULT_FAILED);
	}
	
	function IsBlocked()
	{
		global $model_factory;
		
		$trace = $model_factory->getObject('TaskTraceTask');
		$task_it = $trace->getObjectIt( $this );

		while ( !$task_it->end() )
		{
			if ( !$task_it->IsFinished() )
			{
				return true;
			}
			$task_it->moveNext();
		}
		
		return false;
	}
	
	function getPrecedingIt()
	{
		global $model_factory;
		
		$trace = $model_factory->getObject('TaskTraceTask');
		return $trace->getObjectIt( $this );
	}
	
  	function getProgress()
 	{
 		return array ($this->get('Fact') + $this->get('LeftWork'), $this->get('Fact'));
 	}
 	
 	function getStatesArray()
	{
		global $model_factory;
		
		$tasks = array();
 		$has_tasks = false;
 		$tasks_total = $tasks;
 		$tasks_resolved = $tasks;
 		$phases = $tasks;
 		
 		while ( !$this->end() ) 
 		{
 		    if ( $this->get('TaskType') == '' )
 		    {
 		        $this->moveNext();
 		        continue;
 		    }
 		    
 			$tasks[$this->get('TaskType')] += 1; 
				
 			$items = $this->get('Planned') > 0 ? $this->get('Planned') : 1;
 				
			if( $this->IsFinished() ) 
	 		{
	 			$tasks_resolved[$this->get('TaskType')] += $items;
	 		}
			
	 		$tasks_total[$this->get('TaskType')] += $items;
				
			$phases[$this->get('TaskType')] = translate($this->get('TaskTypeShortName'));
			
			$has_tasks = true;
			
 			$this->moveNext();
 		}

		$state = array();
 		$tasks_keys = array_keys($tasks);
		
 		for ( $i = 0; $i < count($tasks_keys); $i++ ) 
 		{
 			$phase = $phases[$tasks_keys[$i]];
			
			if ( $tasks[$tasks_keys[$i]] == $tasks_resolved[$tasks_keys[$i]] ) 
			{
				$progress = '100%';
			}
			else 
			{
				$progress = round($tasks_resolved[$tasks_keys[$i]] 
					/ $tasks_total[$tasks_keys[$i]] * 100).'%';
			}
			
			if( $tasks[$tasks_keys[$i]] > 0 )
			{
				array_push( $state, 
					array( 'name' => $phase, 
						   'progress' => $progress,
						   'type' => $tasks_keys[$i] ) );
			}
 		}

 		return $state;
	} 	

	function IsTransitable()
	{
		return true;
	}
}