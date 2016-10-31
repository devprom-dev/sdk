<?php

include "StateBaseIterator.php";
include "StateBaseRegistry.php";
include "predicates/StateClassPredicate.php";
include "predicates/StateNeighborsPredicate.php";
include "predicates/StateObjectPredicate.php";
include "predicates/StateSharedVpdPredicate.php";
include "predicates/StateTerminalPredicate.php";
include "predicates/ObjectStatePredicate.php";
include "predicates/StateHasNoTransitionsPredicate.php";
include "predicates/StateHasNoObjectsPredicate.php";
include "predicates/StateTransitionTargetPredicate.php";
include "StateBaseModelBuilder.php";

class StateBase extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_State', new StateBaseRegistry($this));
 		$this->defaultsort = " OrderNum ASC ";
 	}
 	
 	function createIterator() 
 	{
 		return new StateBaseIterator( $this );
 	}
 	
 	function getObjectClass()
 	{
 		return '';
 	}

	function getDefaultAttributeValue( $attr )
	{
 		switch ( $attr )
 		{
 			case 'ObjectClass':
 				return $this->getObjectClass();
 				
 			case 'ReferenceName':
 				return uniqid('State_');
 				
 			default:
 				return parent::getDefaultAttributeValue( $attr ); 
 		}
	}
	
	function getExact( $id )
	{
		if ( is_numeric( $id ) || is_array( $id ) )
		{
			return parent::getExact($id);
		}
		else
		{
			return $this->getByRef('ReferenceName', $id);
		}
	}
	
	function getPage()
	{
		return getSession()->getApplicationUrl($this).'project/workflow/'.get_class($this).'?';
	}

 	function getPageNameObject( $object_id = '' )
	{
		return parent::getPageNameObject().'&entity='.get_class($this);
	}
	
	function getSuitableToRoles( $roles )
	{
		$roles = array_filter($roles, function($value) {
				return $value > 0;
		});
		
		if ( count($roles) < 1 ) $roles = array(0);
		
		$sql = " SELECT t.* " .
			   "   FROM ".$this->getRegistry()->getQueryClause()." t" .
			   "  WHERE EXISTS (SELECT 1 FROM pm_Transition tn, pm_TransitionRole tr" .
			   "				  WHERE tn.SourceState = t.pm_StateId" .
			   "					AND tn.pm_TransitionId = tr.Transition" .
			   "					AND tr.ProjectRole IN (".join($roles, ',').") )".
			   $this->getVpdPredicate().
			   $this->getFilterPredicate().
			   "  ORDER BY t.OrderNum ASC ";

		return $this->createSQLIterator( $sql );
	}
	
 	function add_parms( $parms )
	{
	    global $model_factory;
	    
	    $result = parent::add_parms( $parms );
	    
	    if ( $result > 0 )
	    {
	        // check objects without any state defined
	        $class = $model_factory->getClass($parms['ObjectClass']);
	        
			if ( class_exists($class, false) )
			{
				$object = $model_factory->getObject($class);
				
				$object_it = $object->getByRefArray( array (
				    'State' => 'NULL' 
				));
				
				while ( !$object_it->end() )
				{
					$object->getRegistry()->Store($object_it, array( 
					    'State' => $parms['ReferenceName'] 
					));

					$object_it->moveNext();
				}
			}
	    }
	    
	    return $result;
	}
 	
	function modify_parms( $id, $parms )
	{
		$was_state_it = $this->getExact( $id );
		
		$result = parent::modify_parms( $id, $parms );
		if ( $result < 1 ) return $result;

		$now_state_it = $this->getExact( $id );
		
		if ( $was_state_it->get('ReferenceName') != $now_state_it->get('ReferenceName') ) {
			$class = getFactory()->getClass($this->getObjectClass());
			if ( class_exists($class, false) )
			{
				$sql = " UPDATE ".getFactory()->getObject($class)->getEntityRefName()." SET State = '".$now_state_it->get('ReferenceName')."' WHERE State = '".$was_state_it->get('ReferenceName')."' AND VPD = '".$now_state_it->get('VPD')."' ";
				DAL::Instance()->Query($sql);
			}
		}

		if ( $was_state_it->get('Caption') != $now_state_it->get('Caption') ) {
			$transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
				array (
					new TransitionStateRelatedPredicate($id)
				)
			);
			while( !$transition_it->end() ) {
				if ( $transition_it->get('Caption') == $was_state_it->get('Caption') ) {
					$transition_it->object->modify_parms($transition_it->getId(),
						array (
							'Caption' => $now_state_it->get('Caption')
						)
					);
				}
				$transition_it->moveNext();
			}
		}

		return $result;
	}
}