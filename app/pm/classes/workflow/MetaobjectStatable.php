<?php

include "StatableIterator.php";
include "predicates/StatePredicate.php";

class MetaobjectStatable extends Metaobject 
{
 	var $states, $project, $state_it;
 	
 	function __construct( $class, ObjectRegistrySQL $registry = null, $metadata_cache = '' )
 	{
 	    parent::__construct($class, $registry, $metadata_cache);
 	    
		$this->addAttribute('StateObject', 'INTEGER', '', false, true);
    	foreach ( array( 'LifecycleDuration', 'StateObject' ) as $attribute ) {
    		$this->addAttributeGroup($attribute, 'system');
    	}
 	}
 
 	function getStatableClassName()
 	{
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
 	
 	function getStates() {
		return WorkflowScheme::Instance()->getStates($this);
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
	
	//----------------------------------------------------------------------------------------------------------
	function getLifecycleDuration()
	{
		$aggregage = new AggregateBase( 'VPD', 'LifecycleDuration', 'AVG' );
		
		$this->addAggregate( $aggregage );
		$it = $this->getAggregated();
		
		return round($it->get( $aggregage->getAggregateAlias() ), 0);
	}
	
	//----------------------------------------------------------------------------------------------------------
	function add_parms( $parms )
	{
		$states = $this->getStates();

		if ( count($states) > 0 ) {
			// workflow is defined
			$parms['State'] = $this->reMapState($states, $parms['State']);
		}

		return parent::add_parms( $parms );
	}

	protected function reMapState( $states, $state )
	{
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
			throw new Exception('Unable assing empty state to the object');
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
		else if ( array_key_exists('State', $parms) && $object_it->get('State') != $parms['State'] ) {
			$parms['State'] = $this->reMapState($this->getStates(), $parms['State']);
			if ( $object_it->get('State') != $parms['State'] ) {
				$this->moveToState($object_it, $parms);
			}
		}

		return parent::modify_parms( $object_id, $parms );
	}
	
	function delete ( $id, $record_version = ''  )
	{
		global $model_factory, $_REQUEST;
		
		$result = parent::delete( $id );
		
		if ( $result > 0 && $this->getStatableClassName() != '' )
		{
			$history = $model_factory->getObject('pm_StateObject');
			
			$it = $history->getByRefArray( array (
				'ObjectId' => $id,
				'ObjectClass' => $this->getStatableClassName()
			));
		
			while ( !$it->end() )
			{
				$_REQUEST['RecordVersion'] = '';
				
				$it->delete();
				$it->moveNext();
			}
		}
		
		return $result;
	}
	
	protected function moveToState( $object_it, & $parms )
	{
		if ( $this->getStateClassName() == '' ) {
			return getFactory()->getObject('StateBase')->getEmptyIterator();
		}
		
        $state_it = getFactory()->getObject($this->getStateClassName())->getRegistry()->Query(
			array(
				new FilterAttributePredicate('ReferenceName', $parms['State']),
				new FilterVpdPredicate($object_it->get('VPD'))
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
		$parms['PersistStateDuration'] = true;
		$parms['StateDurationRecent'] = $self_it->get('StateDurationRecent');

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
					'Caption' => $parms['TransitionComment']
				)
			);
		}
		$parms['StateObject'] = getFactory()->getObject('pm_StateObject')->add_parms( 
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
		
		// calculates duration of the lifecycle time of the object
		//			
		$sql = " SELECT ((SELECT MAX(UNIX_TIMESTAMP(so.RecordCreated)) " .
			   "		   FROM pm_StateObject so, pm_State s " .
			   "		  WHERE so.ObjectId = " .$object_it->getId().
			   "			AND so.State = s.pm_StateId " .
			   "			AND s.ObjectClass = '".$this->getStatableClassName()."'" .
			   "			AND s.IsTerminal = 'Y'" .
			   "			AND s.VPD = '".$object_it->get('VPD')."') " .
			   "		- UNIX_TIMESTAMP('".$object_it->get_native('RecordCreated')."')) / 3600 CycleTime ";
		$it = $this->createSQLIterator( $sql );
		
		$parms['LifecycleDuration'] = round($it->get('CycleTime'), 0);
		
		return $state_it;
	}
}
