<?php

class StateDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();

 		array_push( $columns,
 			"( SELECT s.Caption FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias.".State AND s.VPD = ".$alias.".VPD AND s.ObjectClass = '".get_class($this->getObject())."') StateName " );

 		array_push( $columns, 
 			"( SELECT s.RelatedColor FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias.".State AND s.VPD = ".$alias.".VPD AND s.ObjectClass = '".get_class($this->getObject())."') StateColor " );
 		
 		array_push( $columns, 
 			"( SELECT s.IsTerminal FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias.".State AND s.VPD = ".$alias.".VPD AND s.ObjectClass = '".get_class($this->getObject())."') StateTerminal " );
 		
 		return $columns;
 	}
}