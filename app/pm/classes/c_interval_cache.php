<?php
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class IntervalCache
 {
 	var $object_it, $interval_it;
 	
 	function IntervalCache( $object_it ) 
 	{
 		$this->object_it = $object_it;
 	}

	function getIterator()
	{ 	
		return $this->interval_it;
	}
	
 	function store( $dates_sql )
 	{
		$sql = " CREATE TEMPORARY TABLE tmp_CacheInterval (" .
			   "	ObjectId INTEGER, GroupId INTEGER, StartDate DATE, FinishDate DATE, OriginalFinish DATE, " .
			   "	StartDay INTEGER, StartMonth INTEGER, StartQuarter INTEGER, StartYear INTEGER, ".
			   "	OriginalFinishDay INTEGER, OriginalFinishMonth INTEGER, OriginalFinishQuarter INTEGER, OriginalFinishYear INTEGER, ".
			   "	FinishDay INTEGER, FinishMonth INTEGER, FinishQuarter INTEGER, FinishYear INTEGER ) ";

		$this->object_it->object->createSQLIterator( $sql );

		$sql = " INSERT INTO tmp_CacheInterval (" .
			   "	ObjectId, GroupId, StartDate, FinishDate, OriginalFinish)" .
			   $dates_sql;

		$this->object_it->object->createSQLIterator( $sql );
		
		$sql = " UPDATE tmp_CacheInterval " .
			   "	SET StartMonth = MONTH(StartDate), StartDay = DAY(StartDate), " .
			   "		StartQuarter = QUARTER(StartDate), StartYear = YEAR(StartDate), ".
			   "	    FinishMonth = MONTH(FinishDate), FinishDay = DAY(FinishDate), " .
			   "		FinishQuarter = QUARTER(FinishDate), FinishYear = YEAR(FinishDate), ".
			   "	    OriginalFinishMonth = MONTH(OriginalFinish), OriginalFinishQuarter = QUARTER(OriginalFinish), " .
			   "		OriginalFinishYear = YEAR(OriginalFinish) ";

		$this->object_it->object->createSQLIterator( $sql );

		$sql = " SELECT t.* FROM tmp_CacheInterval t ORDER BY GroupId, StartDate ";
			   
		$this->interval_it = $this->object_it->object->createSQLIterator( $sql );
		$this->interval_it->buildPositionHash( array('ObjectId', 'GroupId') );
 	}
 }
 
?>