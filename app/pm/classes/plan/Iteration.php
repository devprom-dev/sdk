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
		        new SortAttributeClause('Caption')
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
		switch( $name ) {
            case 'Project':
                return getSession()->getProjectIt()->getId();
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

		$result = parent::add_parms( $parms );
		
		if ( $result < 1 ) return $result;
		
		$this->getExact($result)->storeMetrics();
		
		return $result;
	}
	
	function modify_parms( $object_id, $parms ) 
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        $object_it = $this->getExact( $object_id );

        if ( $methodology_it->HasFixedRelease() ) {
            // automatically calculate finish date
            $weeks = $methodology_it->get('ReleaseDuration');
            if ( $weeks < 1 ) $weeks = 4;

            $parms['FinishDate'] = date( 'Y-m-j',
                strtotime('-1 day',
                    strtotime($weeks.' week', strtotime( $object_it->get_native('StartDate') ) ) )
            );
        }
        else {
            if ( $parms['StartDate'] != '' && $parms['FinishDate'] == '' && $object_it->get('FinishDate') != '' ) {
                $nowStart = new DateTime($parms['StartDate']);
                $wasStart = new DateTime($object_it->get('StartDate'));
                $interval = $wasStart->diff($nowStart);
                $parms['FinishDate'] = date('Y-m-d',
                                            strtotime($interval->format('%R%a days'),
                                                strtotime($object_it->get('FinishDate')))
                                        );
            }
        }

		$result = parent::modify_parms( $object_id, $parms );
		if ( $result < 1 ) return $result;

        $object_it = $this->getExact( $object_id );

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