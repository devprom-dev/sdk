<?php

class RequestIterator extends StatableIterator
{
	function getDisplayName()
	{
	    $result = parent::getDisplayName();
	 	if ( $this->get('TypeName') != '' ) {
            $result = $this->get('TypeName').': '.$result;
	 	}
	 	return $result;
	}

	function getDisplayNameExt( $prefix = '' )
    {
        if ( $this->get('Deadlines') != '' && $this->get('DueWeeks') < 4 ) {
            $prefix .= '<span class="label '.($this->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'" title="'.$this->object->getAttributeUserName('DeliveryDate').'">';
            $prefix .= $this->getDateFormatShort('DeliveryDate');
            $prefix .= '</span> ';
        }
        $title = parent::getDisplayNameExt($prefix);
        if ( $this->get('ClosedInVersion') != '' ) {
            $title = ' <span class="badge badge-uid badge-inverse">'.$this->get('ClosedInVersion').'</span> ' . $title;
        }

        if ( $this->get('TagNames') != '' ) {
            $tags = array_map(function($value) {
                return ' <span class="label label-info label-tag">'.$value.'</span> ';
            }, preg_split('/,/', $this->get('TagNames')));
            $title = join('',$tags) . $title;
        }

        if ( $this->get('TypeReferenceName') != '' ) {
            $title = '<i class="issue '.$this->get('TypeReferenceName').'"></i> '.$title;
        }

        return $title;
    }

    function getObjectDisplayName() {
        return $this->get('TypeName') != '' ? $this->get('TypeName') : parent::getObjectDisplayName();
    }

 	function IsResolved()
 	{
 		return $this->get_native('State') == 'resolved';
 	}

 	function IsFinished() {
 		return $this->get('StateTerminal') == 'Y';
 	}

	function IsTransitable()
	{
		return true;
	}
 	
	function getImplementationIds()
	{
		$result = array();
		$items = preg_split('/,/', $this->get('LinksWithTypes'));
		foreach( $items as $item ) {
			list($title, $id, $link_type, $state, $blocked) = preg_split('/:/', $item);
			if ( $link_type == 'implemented' ) {
				$result[] = $id;
			}
		}
		return $result;
	}


	/*
	 *  Returns the planned duration of all tasks related to the issue 
	 */ 	
 	function getPlannedDuration()
 	{
 		$duration = 0;
		if ( $this->object->getAttributeType('Tasks') == '' ) return $duration;

 		$task_it = $this->getRef('Tasks');
 		while ( !$task_it->end() && $task_it->get('ChangeRequest') == $this->getId() ) {
			$duration += $task_it->get("Planned");
 			$task_it->moveNext();
 		}	
 		return $duration;
 	} 	

 	 function getPlannedWorkload()
 	 {	
 	 	$ids = array();
		$this->moveFirst();
		
		for( $i = 0; $i < $this->count(); $i++ )
		{
			array_push($ids, $this->getId());
			$this->moveNext();
		}
		
		return $this->object->getPlannedWorkload( $ids );
 	 }

 	 function getBuild()
 	 {
 	 	if ( $this->get('Build') > 0 )
 	 	{
 	 		$build_it = $this->getRef( 'Build' );
 	 		return $build_it->getFullNumber();
 	 	}
 	 	else
 	 	{
 	 		return '';
 	 	}
 	}

 	function getProgress()
 	{
 		global $model_factory;
 		
 		$task = $model_factory->getObject('pm_Task');
 		$types_map = $task->getTypesMap();
 		
 		$ids = array();
 		while ( !$this->end() )
 		{
 			array_push($ids, $this->getId());
 			$this->moveNext();
 		}
 		
 		if ( count($ids) < 1 )
 		{
 			array_push($ids, 0);
 		}
 		
		$sql = " SELECT COUNT(o.ChangeRequest) Total, SUM(o.Resolved) Resolved, o.Kind " .
			   "  FROM ( SELECT t.ChangeRequest, (CASE t.State WHEN 'resolved' THEN 1 ELSE 0 END) Resolved, " .
			   		   "		CASE t.TaskType " .
			   		   "			WHEN '".$types_map['support']."' THEN 'D'" .
			   		   "		 	WHEN '".$types_map['development']."' THEN 'D'" .
			   		   "		 	WHEN '".$types_map['analysis']."' THEN 'A'" .
			   		   "		 	WHEN '".$types_map['design']."' THEN 'A'" .
			   		   "		 	WHEN '".$types_map['documenting']."' THEN 'H'" .
			   		   "		 	WHEN '".$types_map['testing']."' THEN 'T'" .
			   		   " 		END Kind " .
		 			   "   FROM pm_Task t" .
		 			   "  UNION ".
		 		       " SELECT r.pm_ChangeRequestId, CASE r.State WHEN 'resolved' THEN 1 ELSE 0 END Resolved, 'R' Kind " .
		 			   "   FROM pm_ChangeRequest r ) o " .
			   " WHERE o.ChangeRequest IN (".join(',', $ids).")" .
			   " GROUP BY o.Kind ";

 		$it = $this->object->createSQLIterator( $sql );
 		$result = array();
 		
 		while ( !$it->end() )
 		{
 			$result[$it->get('Kind')] = array( $it->get('Total'), $it->get('Resolved') );

 			$it->moveNext();
 		}

 		return $result;
 	}
}