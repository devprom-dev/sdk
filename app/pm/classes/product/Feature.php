<?php

include "FeatureIterator.php";
include "predicates/FeatureStateFilter.php";
include "predicates/FeatureStageFilter.php";
include "sorts/SortFeatureStartClause.php";

class Feature extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_Function');
 		
 		$this->setSortDefault( new SortAttributeClause('Caption') );
 	}

	function createIterator() 
	{
		return new FeatureIterator( $this );
	}

	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'features/list?';
	}
	
	function IsAttributeRequired( $att_name ) 
	{
		if ( $att_name == 'Description' )
		{
			return false;
		}
		
		return parent::IsAttributeRequired( $att_name );
	}

	function getByRequests( $request_it )
	{
		$requests = array();
		$request_it->moveFirst();
		for($i = 0; $i < $request_it->count(); $i++) {
			array_push($requests, $request_it->getId());
			$request_it->moveNext();
		}
		
		if($request_it->count() < 1) array_push($requests, 0);

		$sql = 'SELECT DISTINCT f.* ' .
			   '  FROM pm_Function f INNER JOIN pm_ChangeRequest r ON f.pm_FunctionId = r.Function '.
			   ' WHERE r.pm_ChangeRequestId IN ('.join($requests, ',').')' .
			   ' ORDER BY f.RecordModified DESC';

		return $this->createSQLIterator($sql);
	}
	
	function getDatesSql()
	{	
		return " SELECT t.pm_FunctionId, ".
		       "        t.pm_FunctionId, " .
			   "		t.StartDate, " .
			   "		t.DeliveryDate," .
			   "		NULL" .
			   "   FROM (SELECT ".$this->getRegistry()->getSelectClause('t').
		       "           FROM pm_Function t WHERE 1=1 ".
		                   $this->getVpdPredicate('t').$this->getFilterPredicate().
		       "        ) t ";
	}
	
	function getVersionsSql()
	{
		$sql = " SELECT t.pm_FunctionId, rl.Version, rl.pm_ReleaseId `Release` ".
			   "   FROM pm_Function t, pm_ChangeRequest req, pm_Task ts, pm_Release rl ".	
			   "  WHERE req.Function = t.pm_FunctionId ".
			   "    AND ts.ChangeRequest = req.pm_ChangeRequestId " .
 			   "	AND ts.Release = rl.pm_ReleaseId ".
			   $this->getVpdPredicate().
			   "  UNION ".
			   " SELECT t.pm_FunctionId, req.PlannedRelease, NULL ".
			   "   FROM pm_Function t, pm_ChangeRequest req ".	
			   "  WHERE req.Function = t.pm_FunctionId ".
			   $this->getVpdPredicate().			   
		       "  ORDER BY pm_FunctionId ";
		
		return $sql;
	}
	
	function cacheDeps()
	{
		$this->versions_it = $this->createSQLIterator( $this->getVersionsSql() );
		$this->versions_it->buildPositionHash( array('pm_FunctionId') );
	}
}