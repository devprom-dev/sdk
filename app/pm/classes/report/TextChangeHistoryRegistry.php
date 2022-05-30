<?php

class TextChangeHistoryRegistry extends ObjectRegistrySQL
{
	function getQueryClause(array $parms)
	{
		$startDate = SystemDateTime::date();
		$finishDate = SystemDateTime::date();
		$mapper = new ModelDataTypeMappingDateTime();

		$filters = $this->extractPredicates($parms);
		foreach( $filters as $filter ) {
			if ( $filter instanceof FilterModifiedAfterPredicate ) {
				$startDate = $mapper->map(DAL::Instance()->Escape($filter->getValue()));
			}
			if ( $filter instanceof FilterModifiedBeforePredicate ) {
				$finishDate = $mapper->map(DAL::Instance()->Escape($filter->getValue()));
			}
		}

		return " (SELECT t.pm_TextChangesId,
		 				 t.Author,
		 				 IFNULL(t.ObjectClass, '-') ObjectClass,
		 				 t.ObjectId,
		 				 t.Modified,
		 				 t.Deleted,
		 				 t.Inserted,
		 				 IFNULL(t.VPD, '".$this->getObject()->getVpdValue()."') VPD,
		 				 i.StartDate RecordCreated,
		 				 i.StartDate RecordModified,
		 				 UNIX_TIMESTAMP(i.StartDateOnly) DayDate,
		 				 (SELECT IFNULL(t.Modified / SUM(a.Capacity), 0)
		 				    FROM pm_Activity a
		 				   WHERE a.Participant = t.Author
		 				     AND a.VPD = t.VPD
		 				     AND a.ReportDate = i.StartDateOnly) ModifiedPerHour
					FROM pm_CalendarInterval i 
							LEFT OUTER JOIN pm_TextChanges t 
								ON i.StartDateOnly = DATE(t.RecordModified) ".$this->getFilterPredicate($filters,'t')."
				   WHERE i.Kind = 'day' AND i.StartDate BETWEEN '".$startDate."' AND '".$finishDate."'
				  ) ";
	}
}