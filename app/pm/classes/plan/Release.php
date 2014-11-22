<?php

include "ReleaseIterator.php";
include "ReleaseRegistry.php";
include "predicates/ReleaseTimelinePredicate.php";
include "persisters/ReleaseMetricsPersister.php";
include "sorts/SortReleaseEstimatedStartClause.php";

class Release extends Metaobject
{
 	function Release() 
 	{
		parent::Metaobject('pm_Version', new ReleaseRegistry($this));
		
		$this->setSortDefault( array( new SortAttributeClause('StartDate'), new SortAttributeClause('Caption')) );
		 
		$this->addAttribute('EstimatedStartDate', 'DATETIME', translate('ќценка начала'), false, false);
		$this->addAttribute('EstimatedFinishDate', 'DATETIME', translate('ќценка окончани€'), false, false);

		$this->addPersister( new ReleaseMetricsPersister() );
	}

	function createIterator() 
	{
		return new ReleaseIterator( $this );
	}

	function getDefaultAttributeValue( $name ) 
	{
		global $model_factory, $_REQUEST, $project_it;

		switch ( $name )
		{
			case 'Project':
				
			    return getSession()->getProjectIt()->getId();
				
			case 'StartDate':
				
			    $release = $model_factory->getObject('Release');
				
				$release->addFilter( new ReleaseTimelinePredicate('not-passed') );
				$release->addSort( new SortAttributeClause('StartDate.D') );
				
				$release_it = $release->getAll();
				
				if ( $release_it->count() < 1 )
				{
					return date( 'Y-m-j' );
				}
				else
				{
					return  $release_it->get('EstimatedFinishDate') != '' 
					            ? date( 'Y-m-j', strtotime('1 day', strtotime($release_it->get('EstimatedFinishDate')))) : 
					                $release_it->get('FinishDate') != '' 
					                    ? date( 'Y-m-j', strtotime('1 day', strtotime($release_it->get('FinishDate')))) : date( 'Y-m-j' );
				}

			case 'InitialVelocity':
				return 0;
		}

		return parent::getDefaultAttributeValue($name);
	}

	function getDefaultFinishDate( $start_date, $finish_date = '' )
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		if ( $methodology_it->HasFixedRelease() && !$methodology_it->HasPlanning() )
		{
			$weeks = $methodology_it->get('ReleaseDuration');
			
			if ( $weeks < 1 ) $weeks = 4;

			return date( 'Y-m-d H:i:s', strtotime('-1 day', 
							strtotime($weeks.' week', strtotime( $start_date ) ) )
				   );
		}
		
		return $finish_date;
	}
	
	function getPage() 
	{
		return getSession()->getApplicationUrl().'plan/hierarchy?';
	}

	function cacheDeps()
	{
		$sql = " CREATE TEMPORARY TABLE tmp_ReleaseInterval (" .
			   "	pm_VersionId INTEGER, Project INTEGER, StartDate DATE, FinishDate DATE, OriginalFinish DATE, " .
			   "	StartDay INTEGER, StartMonth INTEGER, StartQuarter INTEGER, StartYear INTEGER, ".
			   "	OriginalFinishDay INTEGER, OriginalFinishMonth INTEGER, OriginalFinishQuarter INTEGER, OriginalFinishYear INTEGER, ".
			   "	FinishDay INTEGER, FinishMonth INTEGER, FinishQuarter INTEGER, FinishYear INTEGER ) ";

		$this->createSQLIterator( $sql );

		$sql = " INSERT INTO tmp_ReleaseInterval (" .
			   "	pm_VersionId, Project, StartDate, FinishDate, OriginalFinish)" .
		       " SELECT r.pm_VersionId, r.Project, " .
			   "		(SELECT m.MetricValueDate " .
			   " 		   FROM pm_VersionMetric m" .
			   "	      WHERE m.Version = r.pm_VersionId" .
			   "			AND m.Metric = 'EstimatedStart')," .
			   "		(SELECT m.MetricValueDate " .
			   " 		   FROM pm_VersionMetric m" .
			   "	      WHERE m.Version = r.pm_VersionId" .
			   "			AND m.Metric = 'EstimatedFinish')," .
			   "		r.FinishDate" .
			   "   FROM pm_Version r";

		$this->createSQLIterator( $sql );
		
		$sql = " UPDATE tmp_ReleaseInterval " .
			   "	SET StartMonth = MONTH(StartDate), StartDay = DAY(StartDate), " .
			   "		StartQuarter = QUARTER(StartDate), StartYear = YEAR(StartDate), ".
			   "	    FinishMonth = MONTH(FinishDate), FinishDay = DAY(FinishDate), " .
			   "		FinishQuarter = QUARTER(FinishDate), FinishYear = YEAR(FinishDate), ".
			   "	    OriginalFinishMonth = MONTH(OriginalFinish), OriginalFinishQuarter = QUARTER(OriginalFinish), " .
			   "		OriginalFinishYear = YEAR(OriginalFinish), OriginalFinishDay = DAY(OriginalFinish) ";

		$this->createSQLIterator( $sql );

		$sql = " SELECT t.* FROM tmp_ReleaseInterval t";
			   
		$this->interval_it = $this->createSQLIterator( $sql );
		$this->interval_it->buildPositionHash( array('pm_VersionId', 'Project') );
	}
	
	function DeletesCascade( $object )
	{
	    switch ( $object->getEntityRefName() )
	    {
	        case 'pm_VersionMetric':
	        case 'pm_Release':
	        	return true;
                	                
	        default:
	            return false;
	    }
	}
	
	function add_parms( $parms )
	{
		$parms['FinishDate'] = $this->getDefaultFinishDate($parms['StartDate'], $parms['FinishDate']);
		
		$object_id = parent::add_parms( $parms );
		
		if ( $object_id > 0 )
		{
			$object_it = $this->getExact( $object_id );

			$object_it->storeMetrics();
		}

		return $object_id;
	}
	
	function modify_parms( $object_id, $parms )
	{
		global $model_factory;
		
		$was_object_it = $this->getExact( $object_id );
		
		$parms['FinishDate'] = $this->getDefaultFinishDate($parms['StartDate'], $parms['FinishDate']);
		
		$result = parent::modify_parms( $object_id, $parms );
		
		if ( $result < 1 ) return $result;
		
		$object_it = $this->getExact( $object_id );
		
		if ( $parms['StartDate'] != '' )
		{
			$object_it->resetBurndown();
		}

		$object_it->storeMetrics();
		
		// update "Resolved in" field across all issues were resolved in the release
		if ( $was_object_it->getDisplayName() != $object_it->getDisplayName() )
		{
			$request = $model_factory->getObject('pm_ChangeRequest');
			$request->removeNotificator('EmailNotificator');
			
			$request->addFilter( 
				new FilterAttributePredicate("ClosedInVersion", $was_object_it->getDisplayName()) );
				 
			$request_it = $request->getAll();

			while( !$request_it->end() )
			{
				$request->modify_parms($request_it->getId(), array (
					"ClosedInVersion" => $object_it->getDisplayName()
				));
				$request_it->moveNext();
			}
		}

		return $result;
	}
}