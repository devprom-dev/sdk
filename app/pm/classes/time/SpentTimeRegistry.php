<?php

class SpentTimeRegistry extends ObjectRegistrySQL
{
	function buildIterator( $it, $days_map, $activities_map, $comments_map )
	{
		$it->setDaysMap($days_map);
		
		$it->setActivitiesMap($activities_map);
		
		$it->setCommentsMap($comments_map);

		return $it;
	}
	
 	function createSQLIterator( $sql ) 
 	{
		$this->report_year = $this->getObject()->getReportYear();
		$this->report_month = $this->getObject()->getReportMonth();
		$this->view = $this->getObject()->getView();
		
		$sql_array = array();
		
		if ( $this->getObject()->getView() == 'participants' || $this->getObject()->getGroup() == 'SystemUser' )
		{
    		$sql = " SELECT p.SystemUser ItemId, 'Participant' Item, p.SystemUser Participant, ".($this->getObject()->getGroup() == 'SystemUser' ? 1 : 2)." SortOrder, p.VPD  " .
    			   "   FROM pm_Participant p ";
    			
    		array_push($sql_array, $sql);
		} 

		if ( $this->getObject()->getView() == 'projects' || $this->getObject()->getGroup() == 'Project' )
		{
    		$projects_sql = 
    			   " SELECT p.Project ItemId, 'Project' Item, p.SystemUser Participant, ".($this->getObject()->getGroup() == 'Project' ? 1 : 2)." SortOrder, p.VPD " .
    			   "   FROM pm_Participant p ";
    				    
    		array_push($sql_array, $projects_sql);
		}

		$group_function = $this->report_month > 0 ? "DAYOFMONTH(%1)" : ($this->report_year > 0 ? "MONTH(%1)" : "YEAR(%1)");
		
 		if ( $this->view == 'tasks' )
		{
			$sql = " SELECT t.pm_TaskId ItemId, 'Task' Item, t.Participant, 3 SortOrder, t.VPD " .
				   "   FROM ( " .
						   " SELECT a.Task pm_TaskId, MIN(".preg_replace('/%1/', "a.ReportDate", $group_function).") SortColumn, ".
						   "        a.Participant, a.VPD " .
						   "   FROM pm_Activity a " .
						   "  WHERE 1 = 1 ".
            			  ($this->report_year > 0 ? " AND YEAR(a.ReportDate) = ".$this->report_year : "").
            			  ($this->report_month > 0 ? " AND MONTH(a.ReportDate) = ".$this->report_month : "").
						   "	AND IFNULL(a.Capacity, 0) > 0 ".
						   "  GROUP BY 1, 3 ORDER BY 2 ASC ".
				   "   		) t";

			array_push($sql_array, $sql);
		}
		else if ( $this->view == 'issues' || count($sql_array) < 1 )
		{
			$sql = " SELECT t.ChangeRequest ItemId, 'ChangeRequest' Item, t.Participant, 3 SortOrder, t.VPD " .
				   "   FROM ( " .
						   " SELECT t.ChangeRequest, MIN(".preg_replace('/%1/', "a.ReportDate", $group_function).") RecordModified, ".
						   "        a.Participant, a.VPD " .
						   "   FROM pm_Activity a, pm_Task t " .
						   "  WHERE 1 = 1 ".
            			  ($this->report_year > 0 ? " AND YEAR(a.ReportDate) = ".$this->report_year : "").
            			  ($this->report_month > 0 ? " AND MONTH(a.ReportDate) = ".$this->report_month : "").
						   "    AND t.pm_TaskId = a.Task " .
						   "	AND IFNULL(a.Capacity, 0) > 0 ".
						   "  GROUP BY 1, 3 ORDER BY 2 ASC ".
				   "   	   ) t ";

			array_push($sql_array, $sql);
		}
		
		$users_ids = getFactory()->getObject('ProjectUser')->getRegistry()
						->Query(array(new FilterInPredicate($this->getObject()->getParticipantFilter())))
							->idsToArray();
		
		$projects_ids = array_filter(
				array_merge(
	 				array(getSession()->getProjectIt()->getId()),
	 				preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject'))
	 			),
				function($value) {
					return $value > 0;
				}
			);

		$sql = " SELECT DISTINCT t.ItemId, t.Item, t.SystemUser, t.Project ".
		       "   FROM (SELECT t.ItemId, t.Item, ".
		       "                ".( $this->getObject()->getGroup() != 'SystemUser' ? 'GROUP_CONCAT(DISTINCT CAST(p.SystemUser AS CHAR))' : 'p.SystemUser')." SystemUser, " .
		       "                ".( $this->getObject()->getGroup() != 'Project' ? 'GROUP_CONCAT(DISTINCT CAST(p.Project AS CHAR))' : 'p.Project')." Project " .
		       "           FROM (".join(' UNION ', $sql_array).") t," .
			   "		        pm_Participant p " .
			   "          WHERE p.SystemUser IN (".join($users_ids,',').")" .
			   "			AND p.Project IN (".join($projects_ids,',').") ".
			   "            AND t.Participant = p.SystemUser ".
			   "			AND t.VPD = p.VPD ".
		       ( $this->getObject()->getGroup() != '' ? 'GROUP BY p.'.$this->getObject()->getGroup().', 1, 2' : 'GROUP BY 1, 2' ).
			   ( $this->getObject()->getGroup() == 'SystemUser'
			   		? "  ORDER BY (SELECT u.Caption FROM cms_User u WHERE u.cms_UserId = p.SystemUser), p.SystemUser, t.SortOrder "
               		: ( $this->getObject()->getGroup() == 'Project'
               				? " ORDER BY (SELECT pr.Caption FROM pm_Project pr WHERE pr.pm_ProjectId = p.Project), p.Project, t.SortOrder  "
               				: "	ORDER BY t.SortOrder " ) 
		       )."       ) t ";

		$it = parent::createSQLIterator($sql);

		// prepare reported days array
		//
		$this->days_map = array();

		$last_date = $this->report_year > 0 
		    ? $this->report_year.'-'.( $this->report_month > 0 ? $this->report_month.'-01' : '12-01' ) 
		    : SystemDateTime::date();
		      		 
		$sql = "SELECT ".preg_replace('/%1/', "LAST_DAY('".$last_date."')", $group_function)." LastDay";

		$last_day_it = parent::createSQLIterator($sql);

		for ( $i = ($this->report_year > 0 ? 1 : date('Y') - 5); $i <= $last_day_it->get('LastDay'); $i++ )
		{
			$this->days_map[] = $i;
		}
	
		// calculate summary activities per report day
		//
		$this->activities_map = array();
		$this->comments_map = array();
		$this->values_map = $this->days_map;
		
		for ( $i = 0; $i < count($this->values_map); $i++ )
		{
			$this->values_map[$i] = '';
		}
		
		if ( !getFactory()->getAccessPolicy()->can_read_attribute(getFactory()->getObject('Task'), 'Planned') )
		{
			return $this->buildIterator($it, $this->days_map, $this->activities_map, $this->comments_map);
		}
		
		$activity = $this->getObject()->getActivityObject();			
		$activity->addFilter( new ActivityReportYearPredicate($this->report_year) );
		$activity->addFilter( new ActivityReportMonthPredicate($this->report_month) );
		$activity->addFilter( new FilterAttributePredicate('Participant', count($this->getObject()->getParticipantFilter()) > 0 ? $this->getObject()->getParticipantFilter() : $users_ids) );
		$activity->addFilter( new FilterVpdPredicate() );
		
		$reported_it = $activity->getReported("SystemUser", $group_function);
		
		while ( !$reported_it->end() )
		{
			$user_key = 'Participant'.$reported_it->get('SystemUser');
			$project_key = 'Project'.$reported_it->get('Project');
			
			$this->activities_map[$user_key][$project_key]
				[$reported_it->get('Day')] = (float) round($reported_it->get('Capacity'), 1);

			$this->comments_map[$user_key][$project_key]
				[$reported_it->get('Day')] = $reported_it->get('Comments');

			$reported_it->moveNext();
		}

		$reported_it = $activity->getReported("Project", $group_function);
		
		while ( !$reported_it->end() )
		{
			$user_key = 'Participant'.$reported_it->get('SystemUser');
			$project_key = 'Project'.$reported_it->get('Project');
			
			$this->activities_map[$project_key][$user_key]
				[$reported_it->get('Day')] = (float) round($reported_it->get('Capacity'), 1);

			$this->comments_map[$project_key][$user_key]
				[$reported_it->get('Day')] = $reported_it->get('Comments');

			$reported_it->moveNext();
		}

		if ( $this->view == 'issues' )
		{
			$reported_it = $activity->getReported("ChangeRequest", $group_function);
			
			while ( !$reported_it->end() )
			{
				$user_key = 'Participant'.$reported_it->get('SystemUser');
				
				$this->activities_map['ChangeRequest'.$reported_it->get('ChangeRequest')][$user_key]
					[$reported_it->get('Day')] = (float) round($reported_it->get('Capacity'), 1);
	
				$this->comments_map['ChangeRequest'.$reported_it->get('ChangeRequest')][$user_key]
					[$reported_it->get('Day')] = $reported_it->get('Comments');

				$reported_it->moveNext();
			}
		}

		if ( $this->view == 'tasks' )
		{
			$reported_it = $activity->getReported("Task", $group_function);
			
			while ( !$reported_it->end() )
			{
				$user_key = 'Participant'.$reported_it->get('SystemUser');
				
				$this->activities_map['Task'.$reported_it->get('Task')][$user_key]
					[$reported_it->get('Day')] = (float) round($reported_it->get('Capacity'), 1);
	
				$this->comments_map['Task'.$reported_it->get('Task')][$user_key]
					[$reported_it->get('Day')] = $reported_it->get('Comments');

				$reported_it->moveNext();
			}
		}

		return $this->buildIterator($it, $this->days_map, $this->activities_map, $this->comments_map);
	}
}