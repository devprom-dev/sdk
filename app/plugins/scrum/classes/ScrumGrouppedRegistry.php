<?php

class ScrumGrouppedRegistry extends ObjectRegistrySQL
{
	function getAll()
	{
 		$sort = $this->getSortClause();
 		
 		$sql = "SELECT t.*, DATE_FORMAT(t.RecordCreated, '".getSession()->getLanguage()->getDateFormat()."') GroupDate" .
 			   "  FROM pm_Scrum t " .
 			   " WHERE t.VPD IN ('".join("','",$this->getObject()->getVpds())."') ".
 			   " ORDER BY ".($sort != '' ? $sort."," : "")." t.RecordCreated DESC ";
 		
 		return $this->createSQLIterator($sql);
	}
}