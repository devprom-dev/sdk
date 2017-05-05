<?php

class SpentTimeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql ) 
 	{
        $startDate = $finishDate = "CURDATE()";
        $mapping = new ModelDataTypeMappingDate();

        foreach( $this->getFilters() as $filter ) {
            if ( $filter instanceof FilterSubmittedAfterPredicate ) {
                $value = $mapping->map($filter->getValue());
                if ( $value != '' ) $startDate = "DATE('".$value."')";
                $filter->setValue('');
            }
            if ( $filter instanceof FilterSubmittedBeforePredicate ) {
                $value = $mapping->map($filter->getValue());
                $finishDate = $value != '' ? "DATE('".$value."')" : 'LAST_DAY(NOW())';
                $filter->setValue('');
            }
        }

		$group_field = $this->getObject()->getGroup();
		if ( $this->getObject()->IsReference($group_field) && $this->getObject()->getAttributeObject($group_field) instanceof User ) {
			$userField = $group_field;
		}
		else {
			$userField = 'SystemUser';
		}

 		$group_function = "TO_DAYS(%1)";
        $group = preg_replace(
            '/%1/',
            "CONVERT_TZ(t2.ReportDate, '".EnvironmentSettings::getUTCOffset().":00', '".EnvironmentSettings::getClientTimeZoneUTC()."')",
            $group_function
        );

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

		$sql = " SELECT ".$registry->getSelectClause('t').",".
			   "        ".$group." Day, ".
		       "		t2.* " .
			   "   FROM (SELECT t.ChangeRequest, a.Task, a.Capacity, a.ReportDate, a.Description, " .
			   "				a.VPD, a.Participant ".$userField.", ".($userField != 'SystemUser' ? "a.Participant SystemUser," : "").
			   " 				(SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = t.VPD) Project" .
			   "		   FROM pm_Activity a, pm_Task t ".
			   "  	      WHERE a.Task = t.pm_TaskId AND DATE(a.ReportDate) BETWEEN ".$startDate." AND ".$finishDate." ".
               $this->getObject()->getVpdPredicate('t').
			   "        ) t2, ".
			   "		".$registry->getQueryClause()." t " .
			   "  WHERE 1 = 1 ".$this->getFilterPredicate('t2').
			   "	AND t2.".$row_field." = t.".$row_object->getIdAttribute();

		$activity_it = parent::createSQLIterator($sql);

		$rows = array();
		$groups = array();

		while( !$activity_it->end() )
		{
			$rows[$activity_it->get($group_field)][$activity_it->get($row_field)]['Day'.$activity_it->get('Day')] 
					+= $activity_it->get('Capacity');
            $rows[$activity_it->get($group_field)][$activity_it->get($row_field)]['Comment'.$activity_it->get('Day')][] =
                array (
                    'Task' => $activity_it->get('Task'),
                    'Text' => $activity_it->get('Description')
                );
			$groups[$activity_it->get($group_field)]['Day'.$activity_it->get('Day')]
					+= $activity_it->get('Capacity');
			$activity_it->moveNext();
		}

		$data = array();
		foreach( $groups as $group_id => $values )
		{
			if ( $group_id != '' ) {
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
        $it->setDaysMap($this->buildDaysMap($startDate, $finishDate));
		return $it;
 	}

	protected function buildDaysMap($startDate, $finishDate)
	{
		$map = array();

        $it = parent::createSQLIterator(
            " SELECT TO_DAYS(t.StartDateOnly) DayId, DAYOFMONTH(t.StartDateOnly) DayName, t.StartDateOnly 
                FROM pm_CalendarInterval t WHERE t.Kind = 'day' AND t.StartDateOnly BETWEEN ".$startDate." AND ".$finishDate."
               ORDER BY t.StartDateOnly ASC "
        );
        while( !$it->end() ) {
            $map[$it->get('DayId')] =
                $it->get('DayName') == 1 || count($map) < 1 || count($map) == $it->count() - 1
                    ? getSession()->getLanguage()->getDateFormattedShort($it->get('StartDateOnly'))
                    : $it->get('DayName');
            $it->moveNext();
        }
        return $map;
	}
}