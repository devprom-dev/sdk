<?php

class WorkItemStatePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();

 		array_push( $columns,
 			"( SELECT s.Caption FROM pm_State s ".
 			"   WHERE s.VPD = ".$alias.".VPD AND s.ObjectClass = t.ObjectClass AND s.ReferenceName = ".$alias.".State ) StateName " );

 		array_push( $columns, 
 			"( SELECT s.RelatedColor FROM pm_State s ".
 			"   WHERE s.VPD = ".$alias.".VPD AND s.ObjectClass = t.ObjectClass AND s.ReferenceName = ".$alias.".State ) StateColor " );
 		
 		array_push( $columns, 
 			"( SELECT s.IsTerminal FROM pm_State s ".
 			"   WHERE s.VPD = ".$alias.".VPD AND s.ObjectClass = t.ObjectClass AND s.ReferenceName = ".$alias.".State ) StateTerminal " );
 		
 		return $columns;
 	}
}