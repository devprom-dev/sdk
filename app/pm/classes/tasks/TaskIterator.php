<?php
include_once SERVER_ROOT_PATH . "pm/classes/workflow/StatableIterator.php";

class TaskIterator extends StatableIterator
{
 	function getDisplayName()
 	{
 		$type_name = $this->getType();
 		$title = $type_name != ""
            ? $type_name.': '.$this->get('Caption')
            : parent::getDisplayName();

        if ( $this->get('TaskAssigneePhotoTitle') != '' ) {
            $title .= ' ['.$this->get('TaskAssigneePhotoTitle').']';
        }

        $stateIt = $this->getStateIt();
        if ( $stateIt->get('Caption') != '' ) {
            $title .= ' ('.$stateIt->get('Caption').')';
        }

        return $title;
    }

    function getDisplayNameExt($prefix = '')
    {
        $priorityColor = parent::get('PriorityColor');
        if ( $priorityColor != '' ) {
            $prefix .= '<span class="pri-cir" style="color:'.$priorityColor.'">&#x25cf;</span>';
        }

        if ( $this->get('PlannedFinishDate') != '' && $this->get('DueWeeks') > -3 && $this->get('DueWeeks') < 4 ) {
            $prefix .= '<span class="label '.($this->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'" title="'.$this->object->getAttributeUserName('PlannedFinishDate').'">';
            $prefix .= $this->getDateFormattedShort('PlannedFinishDate');
            $prefix .= '</span> ';
        }

        if ( $this->get('TagNames') != '' ) {
            $tags = array_map(function($value) {
                return ' <span class="label label-info label-tag">'.$value.'</span> ';
            }, preg_split('/,/', $this->get('TagNames')));
            $prefix = join('',$tags) . $prefix;
        }

        $displayAttributes = array();
        foreach( $this->object->getAttributesByGroup('display-name') as $attribute ) {
            if ( $this->get($attribute) == '' ) continue;
            $displayAttributes[] = '['.$this->get($attribute).']';
        }
        if ( count($displayAttributes) > 0 ) {
            $prefix = $prefix . join(' ', $displayAttributes) . ' ';
        }

        return parent::getDisplayNameExt($prefix);
    }

    function getDisplayNameNative()
	{
		$title = '';
		$type_name = $this->getType();
		if ( $type_name != '' ) {
			$title .= $type_name;
			if ( $this->get('CaptionNative') != '' ) {
				$title .= ': '.$this->get('CaptionNative');
			}
			return $this->getStateTag().$title;
		}
		else {
			return $this->getStateTag().$this->get('CaptionNative');
		}
	}

    function getObjectDisplayName() {
        return $this->get('TaskType') != '' ? $this->getType() : parent::getObjectDisplayName();
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
	
	function IsFinished() {
		return $this->get('FinishDate') != '';
	}
	
	function IsBlocked()
	{
		$trace = getFactory()->getObject('TaskTraceTask');
		$task_it = $trace->getObjectIt( $this );

		while ( !$task_it->end() ) {
			if ( !$task_it->IsFinished() ) return true;
			$task_it->moveNext();
		}
		
		return false;
	}
	
  	function getProgress()
 	{
 		return array ($this->get('Fact') + $this->get('LeftWork'), $this->get('Fact'));
 	}
 	
 	function getStatesArray()
	{
		$tasks = array();
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