<?php

class TransitionIterator extends OrderedIterator
{
 	var $nondoablereason;
 	
 	function getFullName()
 	{
 		$target_it = $this->getRef('TargetState');
 		return parent::getDisplayName().' > '.$target_it->getDisplayName();
 	}
 	
 	function appliable()
 	{
 		if ( !defined('PERMISSIONS_ENABLED') ) return true;
 			
 		$role_it = getFactory()->getObject('pm_TransitionRole')->getByRef('Transition', $this->getId());
 		if ( $role_it->count() < 1 )
 		{
 			return $this->checkDefaultAccess();
 		}
 		
 		$role = getFactory()->getObject('pm_ProjectRole');
 		$role_it = $role_it->count() > 0 ? $role->getExact($role_it->fieldToArray('ProjectRole')) : $role->getEmptyIterator();
 		
 		$roles = getSession()->getParticipantIt()->getBaseRoles();
 		while ( !$role_it->end() )
 		{
 			foreach( $roles as $role_ref => $data )
 			{
 				if ( $role_ref == $role_it->get('ReferenceName') )
 				{
 					return $this->checkDefaultAccess();
 				}
 			}
 			
 			$role_it->moveNext();
 		}

 		return false;
 	}
 	
 	function doable( $object_it )
 	{
 		$this->nondoablereason = '';
 		
 		$predicate_it = getFactory()->getObject('pm_TransitionPredicate')->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('Transition', $this->getId())
 				)
 		);
 		
 		$rule = getFactory()->getObject('StateBusinessRule');
 		
 		while ( !$predicate_it->end() )
 		{
	 		$rule_it = $predicate_it->getRef('Predicate', $rule);
	 			
	 		if ( $rule_it->getId() != '' && !$rule_it->check( $object_it ) ) 
	 		{
	 			$this->nondoablereason = $rule_it->getNegativeReason();
	 			if ( $this->nondoablereason == '' ) $this->nondoablereason = $rule_it->getDisplayName(); 
	 			
	 			return false;
	 		}
	 		
 			$predicate_it->moveNext();
 		}
 		
 		return true;
 	}
 	
 	function getNonDoableReason()
 	{
 		return $this->nondoablereason;
 	}

 	function checkDefaultAccess()
 	{
		return true;				
 	}
}