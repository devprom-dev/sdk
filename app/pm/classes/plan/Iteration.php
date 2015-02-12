<?php

include "IterationIterator.php";
include "IterationRegistry.php";
include "predicates/IterationTimelinePredicate.php";
include "predicates/IterationReleasePredicate.php";
include "predicates/IterationUserHasTasksPredicate.php";
include "sorts/SortRecentNumberClause.php";
include "sorts/SortReleaseIterationClause.php";

class Iteration extends Metaobject
{
 	function __construct() 
 	{
		parent::Metaobject('pm_Release', new IterationRegistry($this));
		
		$this->setSortDefault( array( 
		        new SortReleaseIterationClause(),
		        new SortAttributeClause('ReleaseNumber')
		));
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
		return translate('��������');
	}
	
	function createIterator() 
	{
		return new IterationIterator( $this );
	}

	function getDefaultAttributeValue( $name ) 
	{
		global $_REQUEST, $model_factory;
		
		if ( $name == 'StartDate' ) 
		{
		    if ( $_REQUEST['Version'] > 0 )
		    {
    			$release = $model_factory->getObject('pm_Version');
    			
    			$release_it = $release->getExact($_REQUEST['Version']);
    			
    			if ( $release_it->count() > 0 )
    			{
    				return $release_it->getNextIterationStart();
    			}
		    }
		    
		    return date( 'Y-m-j' );
		} 
		elseif ( $name == 'ReleaseNumber' ) 
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
	
		$result = parent::add_parms( $parms );
		
		if ( $result < 1 ) return $result;
		
		$this->getExact($result)->storeMetrics();
		
		return $result;
	}
	
	function modify_parms( $object_id, $parms ) 
	{
		global $model_factory;

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

		if ( $parms['StartDate'] != '' )
		{
			$object_it->resetBurndown();
		}
		
		$object_it->storeMetrics();
		
		return $result;
	}
	
	function getPage() 
	{
		$url = getSession()->getApplicationUrl($this).'plan/hierarchy?';
		
		if( $_REQUEST['version'] > 0 )
		{
		    $url .= '&version='.$version.'&';
		}
			
		return $url;
	}
}