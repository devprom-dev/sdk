<?php

class StateDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
		$className = $this->getObject()->getStatableClassName();

 		array_push( $columns,
 			"( SELECT s.Caption FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias.".State AND s.VPD = ".$alias.".VPD AND s.ObjectClass = '".$className."') StateName " );

 		array_push( $columns, 
 			"( SELECT s.RelatedColor FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias.".State AND s.VPD = ".$alias.".VPD AND s.ObjectClass = '".$className."') StateColor " );
 		
 		array_push( $columns, 
 			"( SELECT s.IsTerminal FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias.".State AND s.VPD = ".$alias.".VPD AND s.ObjectClass = '".$className."') StateTerminal " );

        array_push( $columns,
            "( SELECT s.ArtifactsType FROM pm_State s ".
            "   WHERE s.ReferenceName = ".$alias.".State AND s.VPD = ".$alias.".VPD AND s.ObjectClass = '".$className."') StateArtifactsType " );

 		return $columns;
 	}
}