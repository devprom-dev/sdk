<?php

class ScrumGrouppedRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
	    return " (SELECT t.*, DATE_FORMAT(t.RecordCreated, '".getSession()->getLanguage()->getDateFormat()."') GroupDate FROM pm_Scrum t) ";
	}
}