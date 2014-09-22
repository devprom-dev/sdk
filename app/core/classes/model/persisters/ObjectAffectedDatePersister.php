<?php

include_once "ObjectSQLPersister.php";

class ObjectAffectedDatePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
		$columns[] = 
			" IFNULL((SELECT CONCAT(RecordModified,'.',co_AffectedObjectsId) ".
			"	 FROM co_AffectedObjects o ".
			"   WHERE o.ObjectId = ".$this->getPK($alias).
			"	  AND o.ObjectClass = '".get_class($this->getObject())."' ".
			"   ORDER BY RecordModified DESC LIMIT 1), t.RecordModified) AffectedDate "; 
 		
 		return $columns;
 	}
}
