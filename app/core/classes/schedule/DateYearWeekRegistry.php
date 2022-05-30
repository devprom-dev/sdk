<?php

class DateYearWeekRegistry extends ObjectRegistrySQL
{
	public function getQueryClause(array $parms)
	{
		return " ( SELECT DISTINCT
		                  YEARWEEK(i.StartDate) entityId,
		 				  STR_TO_DATE(CONCAT(YEARWEEK(i.StartDate), ' Monday'), '%X%V %W') StartDate,
		 				  DATE_ADD(STR_TO_DATE(CONCAT(YEARWEEK(i.StartDate), ' Saturday'), '%X%V %W'), INTERVAL 1 DAY) FinishDate,
		 				  i.pm_CalendarIntervalId OrderNum
				     FROM pm_CalendarInterval i ) ";
	}
}
