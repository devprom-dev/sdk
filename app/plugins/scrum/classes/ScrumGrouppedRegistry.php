<?php

class ScrumGrouppedRegistry extends ObjectRegistrySQL
{
	function getQueryClause(array $parms)
	{
	    return " (SELECT t.*, DATE_FORMAT(t.RecordCreated, '".getSession()->getLanguage()->getDateFormat()."') GroupDate FROM pm_Scrum t) ";
	}
}