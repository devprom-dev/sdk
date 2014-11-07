<?php
 
 /////////////////////////////////////////////////////////////////////////////////
 class CompetitorIterator extends OrderedIterator
 {
 } 
 
 /////////////////////////////////////////////////////////////////////////////////
 class Competitor extends Metaobject
 {
 	function Competitor() 
 	{
 		parent::Metaobject('pm_Competitor');
 		$this->defaultsort = 'Caption ASC';
 	}

	function createIterator() 
	{
		return new CompetitorIterator( $this );
	}

	function getProgressIt()
	{
		$sql = " SELECT t.*, " .
			   "		(SELECT COUNT(1) FROM pm_FeatureAnalysis f " .
			   "		  WHERE f.Competitor = t.pm_CompetitorId ) AnalysedFeatures," .
			   "	 	(SELECT COUNT(1) FROM pm_Function f " .
			   "		  WHERE 1 = 1 ".$this->getVpdPredicate('f').") TotalFeatures" .
			   "   FROM pm_Competitor t " .
			   "  WHERE 1 = 1 ".$this->getVpdPredicate('t');
			   
		return $this->createSQLIterator( $sql );
	}
 }
 
?>