<?php

class IterationRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
	    return " (SELECT t.*, ".
	    	   "	     DATE(t.StartDate) StartDateOnly, ".
	    	   "		 DATE(t.FinishDate) FinishDateOnly, ".
	    	   "		 DATE(GREATEST(NOW(), t.StartDate)) AdjustedStart, ".
	    	   "		 DATE(LEAST(GREATEST(NOW(), t.StartDate), t.FinishDate)) AdjustedFinish ".
	    	   "	FROM pm_Release t ) ";
	}
}