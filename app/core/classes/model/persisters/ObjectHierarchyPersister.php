<?php

class ObjectHierarchyPersister extends ObjectSQLPersister
{
    private $tableName = '';
    private $pk = '';

    function getAttributes() {
        return array('Children');
    }

    function setObject($object)
    {
        $this->tableName = $object->getEntityRefName();
        $this->pk = $object->getIdAttribute();
        parent::setObject($object);
    }

    function IsPersisterImportant() {
        return true;
    }

    function getSelectColumns( $alias ) {
        $parentColumn = array_shift($this->getObject()->getAttributesByGroup('hierarchy-parent'));
 		return array(
 			" IFNULL((SELECT t2.{$this->pk} FROM {$this->tableName} t2 
 			           WHERE t2.{$parentColumn} = ".$this->getPK($alias)." LIMIT 1), 0) ChildrenCount ",

            " (SELECT GROUP_CONCAT(CAST(t2.{$this->pk} AS CHAR)) 
                 FROM {$this->tableName} t2 WHERE t2.{$parentColumn} = ".$this->getPK($alias)." 
                ORDER BY t2.SortIndex) Children "
 		);
 	}

 	function add( $object_id, $parms )
 	{
 		$object_it = $this->getObject()->getExact($object_id);
		$this->updateParentPath($object_it);
		$this->updateSortIndex($object_it);			
 	}

 	function modify( $object_id, $parms )
 	{
        $parentColumn = array_shift($this->getObject()->getAttributesByGroup('hierarchy-parent'));
 		if ( !array_key_exists($parentColumn, $parms) ) return;

 		$object_it = $this->getObject()->getExact($object_id);
		$this->updateParentPath($object_it);
		$this->updateSortIndex($object_it);
 	}
 	
	protected function updateParentPath( $object_it )
	{
        $roots = $object_it->getTransitiveRootArray();
        
        $path_value = ','.join(',', array_reverse($roots)).',';
		$sql = "UPDATE {$this->tableName} t SET t.ParentPath = '{$path_value}' WHERE t.{$this->pk} = {$object_it->getId()}";

		DAL::Instance()->Query( $sql );
		
		$sql = 
			"UPDATE {$this->tableName} t 
			    SET t.ParentPath = REPLACE(t.ParentPath, '{$object_it->get('ParentPath')}', '{$path_value}') 
			  WHERE t.ParentPath LIKE '%,{$object_it->getId()},%' AND t.{$this->pk} <> ".$object_it->getId();

		DAL::Instance()->Query( $sql );
	}
	
	protected function updateSortIndex( $object_it )
	{
        $parentColumn = array_shift($object_it->object->getAttributesByGroup('hierarchy-parent'));
		$parent_id = $object_it->get($parentColumn) != ''
                        ? $object_it->get($parentColumn)
                        : $object_it->getId();
		
		$sql = "CREATE TEMPORARY TABLE tmp_{$this->tableName}Sort ({$this->pk} INTEGER, SortIndex VARCHAR(128) ) ENGINE=MEMORY AS 
			    SELECT t.{$this->pk}, 
			           (SELECT GROUP_CONCAT(LPAD(u.OrderNum, 10, '0') ORDER BY LENGTH(u.ParentPath)) 
 		           	   FROM {$this->tableName} u WHERE t.ParentPath LIKE CONCAT('%,',u.{$this->pk},',%')) SortIndex 
			      FROM {$this->tableName} t 
			     WHERE t.ParentPath LIKE '%,{$parent_id},%' ";
				
		DAL::Instance()->Query( $sql );
		
		$sql = " UPDATE {$this->tableName} t 
                    SET t.SortIndex = (SELECT u.SortIndex FROM tmp_{$this->tableName}Sort u WHERE u.{$this->pk} = t.{$this->pk}) 
			      WHERE t.ParentPath LIKE '%,{$parent_id},%' ";

		DAL::Instance()->Query( $sql );
		
		DAL::Instance()->Query( "DROP TABLE tmp_{$this->tableName}Sort" );

        $className = get_class($object_it->object);

        $sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) 
                SELECT NOW(), NOW(), w.VPD, w.{$this->pk}, '{$className}' 
                  FROM {$this->tableName} w 
                 WHERE w.ParentPath LIKE '%,{$parent_id},%' 
                   AND w.{$parentColumn} <> {$parent_id}";

        DAL::Instance()->Query( $sql );
	}
}
