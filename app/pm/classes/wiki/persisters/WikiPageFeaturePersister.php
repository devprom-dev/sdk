<?php

class WikiPageFeaturePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			"(SELECT GROUP_CONCAT(CAST(r.Function AS CHAR)) ".
 			"   FROM pm_ChangeRequest r, pm_ChangeRequestTrace tr " .
			"  WHERE r.pm_ChangeRequestId = tr.ChangeRequest ".
 		    "    AND tr.ObjectId = ".$objectPK." ".
 		    "    AND tr.ObjectClass IN ('".strtolower(get_class($object))."') ) Feature " );

 		return $columns;
 	}
}
