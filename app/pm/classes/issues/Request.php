<?php
include_once SERVER_ROOT_PATH . "pm/classes/workflow/MetaobjectStatable.php";
include "RequestIterator.php";
include "persisters/IssueLinkedIssuesPersister.php";
include "persisters/RequestIterationsPersister.php";
include "predicates/RequestIterationFilter.php";
include "predicates/RequestAuthorFilter.php";
include "predicates/RequestEstimationFilter.php";
include "predicates/RequestTagFilter.php";
include "predicates/RequestTestResultPredicate.php";
include "predicates/RequestTaskTypePredicate.php";
include "predicates/RequestTaskStatePredicate.php";
include "predicates/RequestNonPlannedPredicate.php";
include "predicates/RequestReleasePredicate.php";
include "predicates/RequestDuplicatesOfFilter.php";
include "predicates/RequestImplementationFilter.php";
include "predicates/RequestDependencyFilter.php";
include "predicates/RequestFinishAfterPredicate.php";
include "predicates/RequestOwnerIsNotTasksAssigneeFilter.php";
include "predicates/RequestDependsFilter.php";
include "predicates/RequestSelectivePredicate.php";
include "predicates/RequestHasTasksPredicate.php";
include "sorts/IssueOwnerSortClause.php";
include "sorts/IssueFunctionSortClause.php";
include "validators/ModelValidatorIssueFeatureLevel.php";
include "RequestModelExtendedBuilder.php";

class Request extends MetaobjectStatable 
{
 	function __construct( $registry = null ) {
		parent::__construct('pm_ChangeRequest', $registry, getSession()->getCacheKey());
		$this->setSortDefault(array(
		    new SortAttributeClause('FinishDate'),
            new SortOrderedClause()
        ));
 	}

    function getDisplayName() {
        return getSession()->IsRDD() ? translate('Доработка') : parent::getDisplayName();
    }

	function createIterator() {
		return new RequestIterator( $this );
	}

	function getValidators() {
        return array(
            new ModelValidatorIssueFeatureLevel()
        );
    }

    function getPage() {
		return getSession()->getApplicationUrl($this).'issues/board?mode=request&';
	}
	
	function IsDeletedCascade( $object ) {
		return false;
	}

	function getOrderStep() {
	    return 1;
	}
	
	function getDefaultAttributeValue( $attr_name )
	{
		if( $attr_name == 'Project' ) {
			return getSession()->getProjectIt()->getId();
		}
		return parent::getDefaultAttributeValue( $attr_name );
	}
	
	function add_parms( $parms )
	{
        if ( $parms['EstimationLeft'] == '' ) {
		    $parms['EstimationLeft'] = $parms['Estimation'];
        }
		if ( !\TextUtils::isValueDefined($parms['EmailMessageId']) ) {
            $parms['EmailMessageId'] = '<'.uniqid(strtolower(get_class($this))) . '@alm>';
        }
		if ( !\TextUtils::isValueDefined($parms['Author']) ) {
            $parms['Author'] = getSession()->getUserIt()->getId();
        }

		$request_id = parent::add_parms( $parms );
		
		if ( $parms['Question'] > 0 )
		{
			$trace = getFactory()->getObject('RequestTraceQuestion');
			$trace->add_parms( 
				array( 'ObjectId' => $parms['Question'],
					   'ObjectClass' => $trace->getObjectClass(),
					   'ChangeRequest' => $request_id )
			);
		}

		$this->updateUID($request_id);

		return $request_id;
	}
	
	function modify_parms( $object_id, $parms )
	{
		if ( $parms['Estimation'] != '' ) {
			$parms['EstimationLeft'] = $parms['Estimation'];
		}

		switch ( $parms['State'] )
		{
			default:
				if ( in_array($parms['State'], $this->getTerminalStates()) ) {
					$parms['EstimationLeft'] = 0;
				}
				break;
		}

		return parent::modify_parms( $object_id, $parms );
	}

	function delete( $id, $record_version = ''  )
	{
		$object_it = $this->getExact($id);

		$result = parent::delete( $id );

        if ( $object_it->getId() > 0 ) {
            DAL::Instance()->Query(" DELETE FROM pm_ChangeRequestTrace WHERE ChangeRequest = ".$object_it->getId());
        }

        return $result;
	}

    function updateUID( $objectId )
    {
        if ( $objectId < 1 ) return;
        $uid = new ObjectUID();
        $sql = "UPDATE pm_ChangeRequest w SET w.UID = '".$uid->getObjectUidInt(get_class($this), $objectId)."' WHERE w.pm_ChangeRequestId = ".$objectId;
        DAL::Instance()->Query( $sql );
    }
}
 