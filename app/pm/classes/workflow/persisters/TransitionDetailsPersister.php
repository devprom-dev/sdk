<?php

class TransitionDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array (
			" (SELECT s.ReferenceName FROM pm_State s WHERE s.pm_StateId = t.SourceState) SourceStateReferenceName ",
			" (SELECT s.Caption FROM pm_State s WHERE s.pm_StateId = t.SourceState) SourceStateName ",
			" (SELECT s.ReferenceName FROM pm_State s WHERE s.pm_StateId = t.TargetState) TargetStateReferenceName ",
			" (SELECT s.Caption FROM pm_State s WHERE s.pm_StateId = t.TargetState) TargetStateName ",
			" (SELECT p.CodeName FROM pm_Project p WHERE p.VPD = t.VPD) ProjectCodeName ",
            " ( SELECT GROUP_CONCAT(CAST(a.pm_TransitionActionId AS CHAR)) ".
            "	  FROM pm_TransitionAction a WHERE a.Transition = ".$this->getPK($alias)." ) Actions ",
            " ( SELECT GROUP_CONCAT(CAST(a.pm_TransitionPredicateId AS CHAR)) ".
            "	  FROM pm_TransitionPredicate a WHERE a.Transition = ".$this->getPK($alias)." ) Predicates ",
            " ( SELECT GROUP_CONCAT(CAST(a.pm_TransitionRoleId AS CHAR)) ".
            "	  FROM pm_TransitionRole a WHERE a.Transition = ".$this->getPK($alias)." ) ProjectRoles ",
            " (SELECT s.OrderNum FROM pm_State s WHERE s.pm_StateId = t.TargetState)
                  - (SELECT s.OrderNum FROM pm_State s WHERE s.pm_StateId = t.SourceState) WorkflowDirection ",
        );
 	}

 	function IsPersisterImportant() {
        return true;
    }
}