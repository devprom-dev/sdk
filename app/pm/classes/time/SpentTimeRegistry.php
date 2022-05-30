<?php

class SpentTimeRegistry extends ObjectRegistrySQL
{
 	function Query( $parms = array() )
 	{
        $startDate = $finishDate = "CURDATE()";
        $mapping = new ModelDataTypeMappingDate();

        $filters = $this->extractPredicates($parms);
        foreach( $filters as $filter ) {
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
        $group = preg_replace( '/%1/',"t2.ReportDate", $group_function);

        $row_field = 'ChangeRequest';
        $row_object = $this->getObject()->getRowsObject();
        if ( is_object($row_object) ) {
            switch( $row_object->getEntityRefName() )
            {
                case 'pm_Task':
                    $row_field = 'Task';
                    break;
                case 'cms_User':
                    $row_field = 'SystemUser';
                    break;
                case 'pm_Project':
                    $row_field = 'Project';
                    break;
            }
        }
        else {
            $row_object = getFactory()->getObject('Request');
        }

		if ( !getFactory()->getAccessPolicy()->can_read($row_object) ) {
            $row_field = 'Project';
            $row_object = getFactory()->getObject('Project');
        }

		$registry = $row_object->getRegistry();

		$participantField = $userField != 'SystemUser' ? "a.Participant SystemUser," : "";

		$sql = " SELECT {$registry->getSelectClause(array(), 't')},
			            {$group} Day,
		       		    t2.*
			       FROM (SELECT t.ChangeRequest,
                                IFNULL(r.Caption, (SELECT rc.Caption FROM pm_ChangeRequest rc WHERE rc.pm_ChangeRequestId = t.ChangeRequest)) RequestCaption,
                                t.Caption TaskCaption,
			                    t.Planned TaskPlanned,
                                a.Task,
                                a.Capacity,
                                a.ReportDate,
                                a.Description,
                                a.Issue,
			   				    IFNULL(r.VPD, t.VPD) VPD, a.Participant cms_UserId, a.Participant {$userField}, {$participantField}
			    				(SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = IFNULL(r.VPD, t.VPD)) Project, t.State, u.Rate
			   		               FROM pm_Activity a LEFT OUTER JOIN pm_ChangeRequest r ON a.Issue = r.pm_ChangeRequestId,
                                        pm_Task t,
                                        cms_User u
			     	              WHERE a.Task = t.pm_TaskId AND a.Participant = u.cms_UserId
                                    AND DATE(a.ReportDate) BETWEEN {$startDate} AND {$finishDate} ) t2,
                        (SELECT t.* FROM {$registry->getQueryClause($parms)} t WHERE 1 = 1 {$registry->getFilterPredicate(array(),'t')} ) t
			      WHERE 1 = 1 {$this->getFilterPredicate($filters,'t2')} AND t2.{$row_field} = t.{$row_object->getIdAttribute()} ";

        $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();
		$activity_it = parent::createSQLIterator($sql);

		$rows = array();
		$planned = array();
		$costs = array();
		$groups = array();
		$groupPlanned = array();
        $groupCosts = array();

		while( !$activity_it->end() )
		{
			$rows[$activity_it->get($group_field)][$activity_it->get($row_field)]['Day'.$activity_it->get('Day')]
                += $activity_it->get('Capacity');

            $costs[$activity_it->get($group_field)][$activity_it->get($row_field)]['Day'.$activity_it->get('Day')]
                += $activity_it->get('Capacity') * $activity_it->get('Rate');

            $rows[$activity_it->get($group_field)][$activity_it->get($row_field)]['Comment'.$activity_it->get('Day')][] =
                array (
                    'Task' => $methodologyIt->HasTasks() ? $activity_it->get('Task') : '',
                    'Issue' => $activity_it->get('ChangeRequest'),
                    'Text' => $activity_it->get('Description') != ''
                                ? $activity_it->get('Description')
                                : ($activity_it->get('TaskCaption') != ''
                                        ? $activity_it->get('TaskCaption')
                                        : $activity_it->get('RequestCaption'))
                );

			$groups[$activity_it->get($group_field)]['Day'.$activity_it->get('Day')]
                += $activity_it->get('Capacity');

            $planned[$activity_it->get($group_field)][$activity_it->get($row_field)]['Task'.$activity_it->get('Task')]
                = $activity_it->get('TaskPlanned');

			$activity_it->moveNext();
		}

		$data = array();
		foreach( $groups as $group_id => $values )
		{
			if ( $group_id != '' ) {
                $plannedGroup = 0;
                foreach( $planned[$group_id] as $row ) {
                    $plannedGroup += array_sum($row);
                }

                $costGroup = 0;
                foreach( $costs[$group_id] as $row ) {
                    $costGroup += array_sum($row);
                }

    			$data[] = array_merge(
						array (
							'Item' => $group_field,
							'ItemId' => $group_id,
							$group_field => $group_id,
							'Total' => array_sum($values),
                            'TotalPlanned' => $plannedGroup,
                            'TotalCosts' => $costGroup,
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
                            'TotalPlanned' => array_sum($planned[$group_id][$row_id]),
                            'TotalCosts' => array_sum($costs[$group_id][$row_id]),
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
            " SELECT TO_DAYS(t.StartDateOnly) DayId, DAYOFMONTH(t.StartDateOnly) DayName, TIMESTAMP(t.StartDateOnly) StartDateOnly 
                FROM pm_CalendarInterval t WHERE t.Kind = 'day' AND t.StartDateOnly BETWEEN ".$startDate." AND ".$finishDate."
               ORDER BY t.StartDateOnly ASC "
        );
        while( !$it->end() ) {
            $map[$it->get('DayId')] =
                $it->get('DayName') == 1 || count($map) < 1 || count($map) == $it->count() - 1
                    ? $it->getDateFormattedShort('StartDateOnly')
                    : $it->get('DayName');
            $it->moveNext();
        }
        return $map;
	}
}