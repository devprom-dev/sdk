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
 		if ( $role_it->count() < 1 ) {
 			return $this->checkDefaultAccess();
 		}
 		
 		$role = getFactory()->getObject('pm_ProjectRole');
        $roles = getSession()->getParticipantIt()->getBaseRoles();
 		$role_it = $role_it->count() > 0
            ? $role->getExact($role_it->fieldToArray('ProjectRole'))
            : $role->getEmptyIterator();

        $foundRoles = array_keys($roles);
        $requiredRoles = $role_it->fieldToArray('ReferenceName');

        if ( $this->get('ProjectRolesLogic') == 'all' ) {
            if ( count(array_intersect($requiredRoles, $foundRoles)) == count($requiredRoles) ) {
                return $this->checkDefaultAccess();
            }
        }
        else {
            if ( count(array_intersect($requiredRoles, $foundRoles)) > 0 ) {
                return $this->checkDefaultAccess();
            }
        }

 		return false;
 	}
 	
 	function doable( $object_it, $rules_it = null )
 	{
 		$this->nondoablereason = '';
        $checkResult = array();

        if ( !is_object($rules_it) ) {
            $rules_it = WorkflowScheme::Instance()->getStatePredicateIt($object_it->object);
            $rules_it = $rules_it->object->createCachedIterator($rules_it->getSubset('Transition', $this->getId()));
        }

 		while ( !$rules_it->end() ) {
            $result = $rules_it->check( $object_it );
            $checkResult[] = $result ? 1 : 0;
            if ( !$result ) {
                $reason = $rules_it->getNegativeReason();
                if ( $reason != '' ) {
                    $this->nondoablereason .= $rules_it->getNegativeReason().PHP_EOL;
                }
            }
            $rules_it->moveNext();
 		}

 		return $this->get('PredicatesLogic') == 'any'
            ? array_sum($checkResult) > 0
            : array_sum($checkResult) == count($checkResult);
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