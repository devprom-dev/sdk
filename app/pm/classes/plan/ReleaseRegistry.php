<?php

class ReleaseRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
	    return " (SELECT t.*, ".
	    	   "	     DATE(t.StartDate) StartDateOnly, ".
	    	   "		 DATE(t.FinishDate) FinishDateOnly, ".
	    	   "		 DATE(GREATEST(NOW(), t.StartDate)) AdjustedStart, ".
	    	   "		 DATE(LEAST(GREATEST(NOW(), t.StartDate), t.FinishDate)) AdjustedFinish ".
	    	   "	FROM pm_Version t ".
			   "  WHERE 1 = 1 ".$this->getObject()->getVpdPredicate('t').") ";
	}
}