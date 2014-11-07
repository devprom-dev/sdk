<?php

include "StatableIterator.php";
include "predicates/StatePredicate.php";
include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";

class MetaobjectStatable extends Metaobject 
{
 	var $states, $project, $state_it;
 	
 	private $attrs_cache = array();
 	
 	function __construct( $class, ObjectRegistrySQL $registry = null ) 
 	{
 	    parent::__construct($class, $registry);
 	    
		$this->addAttribute('StateObject', 'INTEGER', '', false, true);
 	    
 		if ( $this->getAttributeType('Project') == '' )
	    {
			$this->addAttribute('Project', 'REF_pm_ProjectId', translate('Проект'), false);
	
			$this->addPersister( new EntityProjectPersister );
	    }
 	    
 	    $attributes = array( 'LifecycleDuration', 'StateObject' );
    	
    	foreach ( $attributes as $attribute )
    	{
    		$this->addAttributeGroup($attribute, 'system');
    	}
		
    	$attributes = array( 'State', 'LifecycleDuration' );
    	
    	foreach ( $attributes as $attribute )
    	{
    		$this->addAttributeGroup($attribute, 'workflow');
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
 	
 	function getStates()
 	{
 	    $state_it = $this->cacheStates();
 	    
 	    return $state_it->fieldToArray('ReferenceName');
 	}
 	
 	function getTerminalStates()
 	{
 		$state_it = $this->cacheStates();
 		
 		return $state_it->getTerminal();	
 	}
 	
 	function getNonTerminalStates()
 	{
 		$state_it = $this->cacheStates();
 		
 		return array_diff($state_it->getNonTerminal(), $state_it->getTerminal());
 	}

 	function cacheStates( $iterator = null )
 	{
 		global $model_factory, $project_it, $session;
 		
 	 	if ( $this->getStateClassName() == '' )
 		{
 		    return $model_factory->getObject('pm_State')->createCachedIterator(array());
 		}
 		
 		$vpd_context = !is_null($iterator) ? $iterator->get('VPD') : $this->getVpdContext();
 		
 		$state = $model_factory->getObject($this->getStateClassName());
 		
 		if ( isset($this->states[$vpd_context]) )
 		{
 		    return $state->createCachedIterator($this->states[$vpd_context]);
 		} 
 		
		$state->setVpdContext( $vpd_context );

		$state_it = $state->getAll();
		
		$this->states[$vpd_context] = $state_it->getRowset();

		$attrs = $model_factory->getObject('pm_TransitionAttribute');
		
		$attrs->setVpdContext( $vpd_context );
		
		$attr_it = $attrs->getAll();
		
		$this->attrs_cache[$vpd_context] = $attr_it->getRowset();
		
		return $state_it;
 	}
 	
	function createIterator() 
	{
		return new StatableIterator($this);
	}
	
	function checkAttributeRequired( $attr, $transition_it )
	{
	    return in_array($attr, $this->getTransitionAttributesRequired( $transition_it ));
	}
	
	function getAttributesRequired( $state_it )
	{
		return $this->getTransitionAttributesRequired(
				getFactory()->getObject('Transition')->getByRef('TargetState', $state_it->getId())
		);
	}
	
	function getTransitionAttributesRequired( $transition_it )
	{
	    $transition_ids = $transition_it->idsToArray();
	    
 		$this->cacheStates();
 		
 		$vpd_context = $this->getVpdContext();
 		
 		$attribute = getFactory()->getObject('pm_TransitionAttribute');
 		
 		$cache = array();
 		
 		if ( is_array($this->attrs_cache[$vpd_context]) )
 		{
     		foreach( $this->attrs_cache[$vpd_context] as $row )
     		{
     		    if ( in_array($row['Transition'], $transition_ids) ) $cache[] = $row;
     		}
 		}
 		
 		$attr_it = $attribute->createCachedIterator($cache); 
 		
		$attribute_it = getFactory()->getObject('StateAttribute')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('State', $transition_it->getRef('TargetState')->getId()),
						new FilterAttributePredicate('IsRequired', 'Y')
				)
		);
		
		$attributes = array_merge(
				$attr_it->fieldToArray( 'ReferenceName' ), 
				$attribute_it->fieldToArray('ReferenceName')
		);
 		
		if ( $transition_it->get('IsReasonRequired') == 'Y' ) $attributes[] = 'TransitionComment';
 		
		return $attributes;
	}

	function getDefaultAttributeValue( $attr )
	{
		global $_REQUEST, $model_factory;

		switch ( $attr )
		{
		 	case 'Transition':
		 	    
		 		return $_REQUEST['Transition'];
		 		
		 	case 'State':
		 	    
		 		if ( $this->getStateClassName() == '' ) return '';
		 		
				$state = $model_factory->getObject($this->getStateClassName());
				
				$state_it = $state->getFirst();
				
				return $state_it->get('ReferenceName');
		}
		
		return parent::getDefaultAttributeValue( $attr );
	}
	
	function getAttributeObject( $attr )
	{
		global $model_factory;
		
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
		global $model_factory;
		
		$state_it = $this->cacheStates();
		
		if ( $state_it->getId() != '' && $parms['State'] == '' )
		{
			$parms['State'] = $state_it->get('ReferenceName');
		}
		
		
		if ( $state_it->getId() != '' && array_key_exists('State', $parms) && $parms['State'] == '' )
		{
			throw new Exception('Unable assing empty state to the object');
		}
		
		return parent::add_parms( $parms );
	}

	function createLike( $object_id )
	{
		global $model_factory;
		
		$id = parent::createLike( $object_id );
		
		$state_it = $this->cacheStates();
		
		$object_it = $this->getExact( $id );
		
		$this->modify_parms( $id, array( 'State' => $state_it->get('ReferenceName') ) );
		
		return $id;
	}
	
	function modify_parms( $object_id, $parms )
	{
		$object_it = $this->getExact($object_id);

		if ( $parms['Transition'] != '' )
		{
			$state_it = getFactory()->getObject($this->getStateClassName())->getRegistry()->Query(
					array (
							new StateTransitionTargetPredicate($parms['Transition'])
					)
			);
			
			if ( $state_it->getId() > 0 ) $parms['State'] = $state_it->get('ReferenceName');
		}
		
		if ( array_key_exists('State', $parms) && $object_it->get('State') != $parms['State'] )
		{
			$state_it = $this->moveToState($object_it, $parms);
		}

		return parent::modify_parms( $object_id, $parms );
	}
	
	function delete ( $id )
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
		if ( $this->getStateClassName() == '' )
		{
			return getFactory()->getObject('StateBase')->getEmptyIterator();
		}
		
        $state_it = getFactory()->getObject($this->getStateClassName())->getRegistry()->Query(
        		array( 
        				new FilterAttributePredicate('ReferenceName', $parms['State']),
        				new FilterVpdPredicate($object_it->get('VPD'))
        		)
        );
        
        if ( $state_it->getId() < 1 ) throw new Exception('Unable assing empty state to the object'); 
		
		$parms['PersistStateDuration'] = true;
		
		$parms['StateDuration'] = $parms['StateDuration'] == '' ? $object_it->get('StateDuration') : $parms['StateDuration']; 
		
		$parms['StateObject'] = getFactory()->getObject('pm_StateObject')->add_parms( 
				array ( 
						'ObjectId' => $object_it->getId(),
						'ObjectClass' => $this->getStatableClassName(),
						'State' => $state_it->getId(),
						'Transition' => $parms['Transition'],
						'Comment' => $parms['TransitionComment'],
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
		
		// reset fields
		//
		$reset_fields = getFactory()->getObject('TransitionResetField');
		
		$reset_fields_it = $reset_fields->getByRef('Transition', $parms['Transition']);
		
		while ( !$reset_fields_it->end() )
		{
			$parms[$reset_fields_it->get('ReferenceName')] = '';
			
			$reset_fields_it->moveNext();
		}

		return $state_it;
	}
}
