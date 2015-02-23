<?php

class WatchersPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$object = $this->getObject();

 		$columns[] = 
 			"(SELECT GROUP_CONCAT(CAST(wt.SystemUser AS CHAR)) FROM pm_Watcher wt " .
			"  WHERE wt.ObjectId = ".$this->getPK($alias).
 			"    AND LCASE(wt.ObjectClass) IN ('".
 					join("','", array ( 
 						strtolower($object->getClassName()),
 						strtolower(get_class($object)))
 					)."') ) Watchers ";

 		$columns[] = 
 			"(SELECT GROUP_CONCAT(wt.Email) FROM pm_Watcher wt " .
			"  WHERE wt.Email IS NOT NULL AND wt.ObjectId = ".$this->getPK($alias).
 			"    AND LCASE(wt.ObjectClass) IN ('".
 					join("','", array ( 
 						strtolower($object->getClassName()),
 						strtolower(get_class($object)))
 					)."') ) WatchersEmails ";
 		
 		return $columns;
 	}
 	
	function delete( $object_id )
 	{
 		$it = getFactory()->getObject('Watcher')->getRegistry()->Query(
 			array (
 				new FilterAttributePredicate('ObjectId', $object_id),
 				new FilterAttributePredicate('ObjectClass', strtolower(get_class($this->getObject())))
 			)
 		);
 		
 		while( !$it->end() )
 		{
 			$it->object->delete($it->getId());
 			$it->moveNext();
 		}
 	}
}
