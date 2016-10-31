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
			" (SELECT p.CodeName FROM pm_Project p WHERE p.VPD = t.VPD) ProjectCodeName "
		);
 	}
}