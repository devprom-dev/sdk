<?php

class SpentTimeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql ) 
 	{
		$this->report_year = $this->getObject()->getReportYear();
		$this->report_month = $this->getObject()->getReportMonth();
		$this->view = $this->getObject()->getView();
 		
		$group_field = $this->getObject()->getGroup();
 		$group_function = $this->report_month > 0 
 				? "DAYOFMONTH(%1)" 
 				: ($this->report_year > 0 ? "MONTH(%1)" : "YEAR(%1)");
 		
		$this->buildDaysMap($group_function);
		
 		switch( $this->getObject()->getView() )
		{
			case 'issues':
				$row_field = 'ChangeRequest';
				$row_object = getFactory()->getObject('Request');
				break;
			case 'participants':
				$row_field = 'SystemUser';
				$row_object = getFactory()->getObject('User');
				break;
			case 'projects':
				$row_field = 'Project';
				$row_object = getFactory()->getObject('Project');
				break;
			default:
				$row_field = 'Task';
				$row_object = getFactory()->getObject('Task');
				break;
		}
		$registry = $row_object->getRegistry();
		$registry->setPersisters(array(
				new CustomAttributesPersister()
		));
		
		$group = preg_replace(
				'/%1/', 
				"CONVERT_TZ(t.ReportDate, '".EnvironmentSettings::getUTCOffset().":00', '".EnvironmentSettings::getClientTimeZoneUTC()."')", 
				$group_function
		);
		
		$sql = " SELECT ".$group." Day, t.*, t2.* " .
			   "   FROM (SELECT t.ChangeRequest, a.Task, a.Capacity, a.ReportDate, " .
			   "				a.VPD, p.SystemUser, p.Project, p.pm_ParticipantId " .
			   "		   FROM pm_Activity a, pm_Task t, pm_Participant p ".
			   "  	      WHERE a.Task = t.pm_TaskId ".
			   "			AND a.Participant = p.SystemUser ".
			   "			AND t.VPD = p.VPD ".
			   "        ) t, ".
			   "		(SELECT ".$registry->getSelectClause('t')." FROM ".$registry->getQueryClause()." t) t2 " .
			   "  WHERE 1 = 1 ".$this->getFilterPredicate().
			   "	AND t.".$row_field." = t2.".$row_object->getIdAttribute();

		$activity_it = parent::createSQLIterator($sql);
		
		$rows = array();
		$groups = array();
		
		while( !$activity_it->end() )
		{
			$rows[$activity_it->get($group_field)][$activity_it->get($row_field)]['Day'.$activity_it->get('Day')] 
					+= $activity_it->get('Capacity');
			$groups[$activity_it->get($group_field)]['Day'.$activity_it->get('Day')] 
					+= $activity_it->get('Capacity');
			$activity_it->moveNext();
		}

		$data = array();
		foreach( $groups as $group_id => $values )
		{
			if ( $group_id > 0 ) {
				$data[] = array_merge(
						array (
							'Item' => $group_field,
							'ItemId' => $group_id,
							$group_field => $group_id,
							'Total' => array_sum($values),
							'Group' => 1
						),
						$values
					);
			}
			
			foreach( $rows[$group_id] as $row_id => $values  )
			{
				$data[] = array_merge(
						array (
							'Item' => $row_field,
							'ItemId' => $row_id,
							$group_field => $group_id,
							'Total' => array_sum($values),
							'Group' => 0
						),
						$values
					);
			}
		}

		$it = $this->createIterator($data);
		$it->setDaysMap($this->days_map);
		
		return $it; 
 	}

	protected function buildDaysMap($group_function)
	{
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
	}
}