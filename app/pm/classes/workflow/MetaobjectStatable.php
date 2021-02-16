<?php
include_once "StatableIterator.php";
include "predicates/StatePredicate.php";
include "predicates/StateNotInPredicate.php";
include "sorts/StateObjectSortClause.php";

class MetaobjectStatable extends Metaobject 
{
 	var $states, $project, $state_it;
 	
 	function __construct( $class, ObjectRegistrySQL $registry = null, $metadata_cache = '' )
 	{
 	    parent::__construct($class, $registry, $metadata_cache);
 	}
 
 	function getStatableClassName() {
 		return strtolower(get_class($this));
 	}
 	
 	function getStateClassName()
 	{
 		switch ( $this->getClassName() )
 		{
 			case 'pm_ChangeRequest':
 				return 'IssueState';
 				
 			case 'pm_Task':
 				return 'TaskState';
 				
 			case 'pm_Question':
 				return 'QuestionState';
 			
 			default:
 				return 'pm_State';
 		}
 	}
 	
 	function getTerminalStates() {
		return WorkflowScheme::Instance()->getTerminalStates($this);
 	}
 	
 	function getNonTerminalStates() {
		return WorkflowScheme::Instance()->getNonTerminalStates($this);
 	}

	function createIterator() {
		return new StatableIterator($this);
	}
	
	function getDefaultAttributeValue( $attr )
	{
		switch ( $attr )
		{
		 	case 'Transition':
		 		return $_REQUEST['Transition'];
		 		
		 	case 'State':
		 		if ( $this->getStateClassName() == '' ) return '';
				return array_shift(WorkflowScheme::Instance()->getStates($this));
		}
		
		return parent::getDefaultAttributeValue( $attr );
	}
	
	function getAttributeObject( $attr )
	{
		switch ( $attr )
		{
		 	default:
		 		return parent::getAttributeObject( $attr );
		}
	}

	function getStates() {
        return WorkflowScheme::Instance()->getStates($this);
    }

	//----------------------------------------------------------------------------------------------------------
	function add_parms( $parms )
	{
	    $states = WorkflowScheme::Instance()->getStates($this);
        if ( !in_array($parms['State'], $states) ) $parms['State'] = array_shift($states);

		if ( count($states) > 0 ) {
            $projectIt = getSession()->getProjectIt();
            if ( $parms['Project'] != '' && $parms['Project'] != $projectIt->getId() ) {
                $projectIt = getFactory()->getObject('Project')->getExact($parms['Project']);
                $parms['State'] = $this->reMapState($projectIt->get('VPD'), $parms['State']);
            }
		}

		return parent::add_parms( $parms );
	}

	protected function reMapState( $vpd, $state )
	{
        $stateBase = new StateBase();
        $states = $stateBase->getRegistry()->Query(
                array(
                    new StateClassPredicate($this->getStatableClassName()),
                    new FilterVpdPredicate($vpd),
                    new SortOrderedClause()
                )
            )->fieldToArray('ReferenceName');

		if ( $state == '' ) {
			$state = array_shift($states);
		}
		else {
			if ( !in_array($state, $states) ) {
				if ( $state == 'resolved' ) {
					$state = array_pop($states);
				}
				else {
					$state = array_shift($states);
				}
			}
		}
        if ( $state == '' ) {
			throw new Exception('Unable assing empty state to the object - ' .
                var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true));
		}
		return $state;
	}

	function modify_parms( $object_id, $parms )
	{
		$object_it = $object_id instanceof OrderedIterator ? $object_id : $this->getExact($object_id);

		if ( $parms['Transition'] != '' ) {
			$state_it = getFactory()->getObject($this->getStateClassName())->getRegistry()->Query(
				array (
					new StateTransitionTargetPredicate($parms['Transition'])
				)
			);
			if ( $state_it->getId() > 0 ) {
				$parms['State'] = $state_it->get('ReferenceName');
			}
			if ( $parms['State'] != '' ) {
				$this->moveToState($object_it, $parms);
			}
		}
		else if ( array_key_exists('State', $parms) && $object_it->get('State') != $parms['State'] )
		{
            $wasState = $parms['State'];
            if ( $parms['VPD'] != '' && $parms['VPD'] != $object_it->get('VPD') ) {
                $parms['State'] = $this->reMapState( $parms['VPD'], $parms['State'] );
            }
			if ( $wasState != '' && $object_it->get('State') != $parms['State'] ) {
				$this->moveToState($object_it, $parms);
			}
		}

		return parent::modify_parms( $object_id, $parms );
	}
	
	function delete ( $id, $record_version = ''  )
	{
		$result = parent::delete( $id );
		
		if ( $result > 0 && $this->getStatableClassName() != '' )
		{
            $it = getFactory()->getObject('pm_StateObject')->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('ObjectId', $id),
                    new FilterAttributePredicate('ObjectClass', $this->getStatableClassName()),
                    new SortRecentClause()
                )
            );
			while ( !$it->end() )
			{
				$it->delete();
				$it->moveNext();
			}
		}
		
		return $result;
	}
	
	public function moveToState( $object_it, & $parms )
	{
		if ( $this->getStateClassName() == '' ) {
			return getFactory()->getObject('StateBase')->getEmptyIterator();
		}
		
        $state_it = getFactory()->getObject($this->getStateClassName())->getRegistry()->Query(
			array(
				new FilterAttributePredicate('ReferenceName', $parms['State']),
				new FilterVpdPredicate($parms['VPD'] != '' ? $parms['VPD'] : $object_it->get('VPD'))
			)
        );
        if ( $state_it->getId() < 1 ) throw new Exception('Unable assing empty state to the object');
		
		$registry = new ObjectRegistrySQL($this);
		$self_it = $registry->Query(
			array(
				new StateDurationPersister(),
				new FilterInPredicate($object_it->getId())
			)
		);

		$comment_id = '';
		if ( $parms['TransitionComment'] != '' )
		{
			$comment = getFactory()->getObject('Comment');
			$comment->setNotificationEnabled(false);
			$comment_id = $comment->add_parms(
				array (
					'ObjectId' => $object_it->getId(),
					'ObjectClass' => get_class($this),
					'AuthorId' => getSession()->getUserIt()->getId(),
					'Caption' => $parms['TransitionComment'],
                    'IsPrivate' => $parms['IsPrivate']
				)
			);
		}

        $objectstate = getFactory()->getObject('pm_StateObject');
		$soRegistry = $objectstate->getRegistry();
        $soRegistry->setLimit(1);
        $stateobject_it = $soRegistry->Query(
            array (
                new FilterAttributePredicate('ObjectId', $object_it->getId()),
                new FilterAttributePredicate('ObjectClass', $this->getStatableClassName()),
                new SortRecentClause()
            )
        );
        if ( $stateobject_it->getId() != '' ) {
            $objectstate->modify_parms(
                $stateobject_it->getId(),
                array( 'Duration' => $self_it->get('StateDurationRecent') )
            );
        }

        $sql =
            "SELECT 
                (IFNULL(
                    (SELECT UNIX_TIMESTAMP(MIN(so.RecordCreated))
                       FROM pm_StateObject so
                      WHERE so.ObjectId = ".$object_it->getId()."
                        AND so.ObjectClass = '".$this->getStatableClassName()."'), UNIX_TIMESTAMP(NOW())
                 ) - UNIX_TIMESTAMP('".$object_it->get('RecordCreated')."')) / 3600 ResponseTime ";
        $parms['LifecycleDuration'] = $this->createSQLIterator($sql)->get('ResponseTime');

        $sql =
            " SELECT IFNULL(SUM(so.Duration),0) LifecycleDuration ".
            "   FROM pm_StateObject so, pm_State st, pm_Transition tr ".
            "  WHERE so.ObjectId = ".$object_it->getId().
            "    AND so.ObjectClass = '".$this->getStatableClassName()."' ".
            "    AND tr.pm_TransitionId = so.Transition ".
            "    AND st.pm_StateId = tr.TargetState ".
            "    AND st.IsTerminal <> 'Y' ".
            "    AND st.ExcludeLeadTime = 'N' ";

		$parms['LifecycleDuration'] += $this->createSQLIterator($sql)->get('LifecycleDuration');

        $parms['StateObject'] = $objectstate->add_parms(
            array (
                'ObjectId' => $object_it->getId(),
                'ObjectClass' => $this->getStatableClassName(),
                'State' => $state_it->getId(),
                'Transition' => $parms['Transition'],
                'CommentObject' => $comment_id,
                'Author' => getSession()->getUserIt()->getId(),
                'VPD' => $object_it->get('VPD')
            )
        );

		return $state_it;
	}
}
