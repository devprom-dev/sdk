<?php
include "IterationIterator.php";
include "IterationRegistry.php";
include "predicates/IterationTimelinePredicate.php";
include "predicates/IterationReleasePredicate.php";
include "predicates/IterationUserHasTasksPredicate.php";
include "sorts/SortRecentNumberClause.php";
include "sorts/SortReleaseIterationClause.php";
include_once "validators/ModelValidatorDatesCausality.php";

class Iteration extends Metaobject
{
 	function __construct() 
 	{
		parent::Metaobject('pm_Release', new IterationRegistry($this));
		
		$this->setSortDefault( array( 
		        new SortReleaseIterationClause(),
				new SortAttributeClause('StartDate'),
		        new SortAttributeClause('ReleaseNumber')
		));
	}

	function getValidators() {
        return array(
            new ModelValidatorDatesCausality()
        );
    }

    function DeletesCascade( $object )
	{
	    switch ( $object->getEntityRefName() )
	    {
	        case 'pm_ParticipantMetrics':
	        case 'pm_IterationMetric':
	            return true;
                	                
	        default:
	            return false;
	    }
	}

	function getDisplayName()
	{
		return translate('Итерация');
	}
	
	function createIterator() 
	{
		return new IterationIterator( $this );
	}

    function getPageFormPopup() {
        return true;
    }

	function getDefaultAttributeValue( $name )
	{
		global $_REQUEST, $model_factory;
		
		if ( $name == 'ReleaseNumber' ) 
		{
			$iteration = $model_factory->getObject('Iteration');
			$iteration->addSort( new SortRecentNumberClause() );
			
			if ( $_REQUEST['Version'] > 0 )
			{
    			$iteration->addFilter( new IterationReleasePredicate($_REQUEST['Version']) );
			}
			
			return max(intval($iteration->getFirst()->get('ReleaseNumber')), 0) + 1;
		}
		elseif ($name == 'Project') 
		{
			return getSession()->getProjectIt()->getId();
		}
		elseif ($name == 'InitialVelocity' && $_REQUEST['Version'] > 0 ) 
		{
			$release = $model_factory->getObject('Release');
			$iteration = $model_factory->getObject('Iteration');
			
			$release_it = $release->getExact($_REQUEST['Version']);
			if ( $release_it->count() > 0 )
			{
				$iterations = $iteration->getByRefArrayCount(
					array( 'Version' => $release_it->getId() ) );
					
				if ( $iterations < 1 )
				{
					return $release_it->get('InitialVelocity');
				}
				else
				{
					return $release_it->getVelocity();
				}
			}

			return getSession()->getProjectIt()->getTeamVelocity();
		}
		
		return parent::getDefaultAttributeValue($name);
	}

	function getVelocitySuggested()
	{
		$this->getRegistry()->setLimit(5);
		$iteration_it = $this->getRegistry()->Query(
			array (
				new IterationTimelinePredicate(IterationTimelinePredicate::PAST),
				new FilterBaseVpdPredicate(),
				new SortAttributeClause('StartDate.D')
			)
		);
		$velocity = $iteration_it->getId() > 0 ? $iteration_it->getVelocity() : 0;
		$average = 0;
		while( !$iteration_it->end() ) {
			$average += $iteration_it->getVelocity();
			$iteration_it->moveNext();
		}
		$average = $iteration_it->count() > 0 ? $average / $iteration_it->count() : 0;

		return array($average, $velocity);
	}

	function add_parms( $parms ) 
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if ( $methodology_it->HasFixedRelease() || $parms['FinishDate'] == '' )
		{
			$weeks = $methodology_it->get('ReleaseDuration');
			
			if ( $weeks < 1 ) $weeks = 4;

			$parms['FinishDate'] = date( 'Y-m-j', strtotime('-1 day',
				   			strtotime($weeks.' week', strtotime( $parms['StartDate'] ) ) ) 
				   );
		}

		if ( $parms['Caption'] != '' && $parms['ReleaseNumber'] == ''  ) {
            $parms['ReleaseNumber'] = $parms['Caption'];
        }

		$result = parent::add_parms( $parms );
		
		if ( $result < 1 ) return $result;
		
		$this->getExact($result)->storeMetrics();
		
		return $result;
	}
	
	function modify_parms( $object_id, $parms ) 
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$result = parent::modify_parms( $object_id, $parms );
		
		if ( $result < 1 ) return $result;
		
		$object_it = $this->getExact( $object_id );
		
		// automatically calculate finish date
		if ( $methodology_it->HasFixedRelease() )
		{
			$weeks = $methodology_it->get('ReleaseDuration');
			if ( $weeks < 1 ) $weeks = 4;

			$parms['FinishDate'] = date( 'Y-m-j', 
				   		strtotime('-1 day',
				   			strtotime($weeks.' week', strtotime( $object_it->get_native('StartDate') ) ) ) 
				   );

			$result = parent::modify_parms ( $object_id, array('FinishDate' => $parms['FinishDate']) );
			if ( $result < 1 ) return $result;
		}

		if ( $parms['StartDate'] != '' ) {
			$object_it->resetBurndown();
		}
		
		$object_it->storeMetrics();
		
		return $result;
	}
	
	function getPage() {
		return getSession()->getApplicationUrl($this).'iterations?';
	}
}