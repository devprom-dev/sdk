<?php

include_once "ObjectSQLPersister.php";

class ObjectAffectedDatePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
		$columns[] = 
			" IFNULL((SELECT UNIX_TIMESTAMP(RecordModified) * 100000 + co_AffectedObjectsId ".
			"	 FROM co_AffectedObjects o ".
			"   WHERE o.ObjectId = ".$this->getPK($alias).
			"	  AND o.ObjectClass = '".get_class($this->getObject())."' ".
			"   ORDER BY RecordModified DESC LIMIT 1), UNIX_TIMESTAMP(t.RecordModified) * 100000) AffectedDate "; 
 		
 		return $columns;
 	}
}
