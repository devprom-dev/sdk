<?php

class DateYearQuarterRegistry extends ObjectRegistrySQL
{
	public function getQueryClause(array $parms)
	{
		return " ( SELECT DISTINCT
                          YEAR(i.StartDate) * 10 + QUARTER(i.StartDate) entityId,
                          CONCAT(QUARTER(i.StartDate), '/', YEAR(i.StartDate)) Caption,
                          MAKEDATE(YEAR(i.StartDate), 1) + INTERVAL QUARTER(i.StartDate) QUARTER - INTERVAL 1 QUARTER StartDate,
                          (SELECT DATE (DATE_SUB( DATE_ADD( CONCAT( YEAR( i.StartDate ), '-01-01'), 
                                INTERVAL QUARTER(i.StartDate) QUARTER ), INTERVAL 1 DAY))) FinishDate,
                          YEAR(i.StartDate) * 10 + QUARTER(i.StartDate) OrderNum
                     FROM pm_CalendarInterval i ) ";
	}
}
