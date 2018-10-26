<?php

class WatchersPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$object = $this->getObject();

 		$columns[] = 
 			"(SELECT GROUP_CONCAT(CAST(wt.pm_WatcherId AS CHAR)) FROM pm_Watcher wt " .
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

    function add($object_id, $parms)
    {
        if ( trim($parms['Watchers']) == '' ) return;

        $registry = getFactory()->getObject('Watcher')->getRegistry();
        foreach( preg_split('/,/', $parms['Watchers']) as $watcher )
        {
            if ( filter_var($watcher, FILTER_VALIDATE_EMAIL) === false ) {
                $userIt = getFactory()->getObject('User')->getByRef('Caption', $watcher);
                if ( $userIt->getId() != '' ) {
                    $registry->Merge(
                        array(
                            'ObjectId' => $object_id,
                            'ObjectClass' => strtolower(get_class($this->getObject())),
                            'SystemUser' => $userIt->getId()
                        )
                    );
                }
            }
            else {
                $registry->Merge(
                    array(
                        'ObjectId' => $object_id,
                        'ObjectClass' => strtolower(get_class($this->getObject())),
                        'Email' => $watcher
                    )
                );
            }
        }
    }

	function afterDelete( $object_it )
 	{
 		$it = getFactory()->getObject('Watcher')->getRegistry()->Query(
 			array (
 				new FilterAttributePredicate('ObjectId', $object_it->getId()),
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
