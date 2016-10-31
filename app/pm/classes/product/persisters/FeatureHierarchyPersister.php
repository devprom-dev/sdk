<?php

class FeatureHierarchyPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" (SELECT COUNT(1) FROM pm_Function t2 WHERE t2.ParentFeature = ".$this->getPK($alias).") ChildrenCount ",
 			" (SELECT tp.ChildrenLevels FROM pm_FeatureType tp WHERE tp.pm_FeatureTypeId = t.Type) ChildrenLevels " 
 		);
 	}

 	function add( $object_id, $parms )
 	{
 		if ( !array_key_exists('ParentFeature', $parms) ) return;
 		
 		$object_it = $this->getObject()->getExact($object_id);
 		
		$this->updateParentPath($object_it);
		$this->updateSortIndex($object_it);			
 	}

 	function modify( $object_id, $parms )
 	{
 		if ( !array_key_exists('ParentFeature', $parms) ) return;
 		
 		$object_it = $this->getObject()->getExact($object_id);
 		
		$this->updateParentPath($object_it);
		$this->updateSortIndex($object_it);
 	}
 	
	protected function updateParentPath( $object_it )
	{
        $roots = $object_it->getTransitiveRootArray();
        
        $path_value = ','.join(',', array_reverse($roots)).',';

		$sql = "UPDATE pm_Function t SET t.ParentPath = '".$path_value."' WHERE t.pm_FunctionId = ".$object_it->getId();

		DAL::Instance()->Query( $sql );
		
		$sql = 
			"UPDATE pm_Function t ".
			"   SET t.ParentPath = REPLACE(t.ParentPath, '".$object_it->get('ParentPath')."', '".$path_value."') ".
			" WHERE t.ParentPath LIKE '%,".$object_it->getId().",%' AND t.pm_FunctionId <> ".$object_it->getId();

		DAL::Instance()->Query( $sql );
	}
	
	protected function updateSortIndex( $object_it )
	{
		$parent_id = $object_it->get('ParentFeature') != '' ? $object_it->get('ParentFeature') : $object_it->getId();
		
		$sql = " CREATE TEMPORARY TABLE tmp_FunctionSort (pm_FunctionId INTEGER, SortIndex VARCHAR(128) ) ENGINE=MEMORY AS ".
			   " SELECT t.pm_FunctionId, ".
			   "        (SELECT GROUP_CONCAT(LPAD(u.OrderNum, 10, '0') ORDER BY LENGTH(u.ParentPath)) ".
 		       "    	   FROM pm_Function u WHERE t.ParentPath LIKE CONCAT('%,',u.pm_FunctionId,',%')) SortIndex ".
			   "   FROM pm_Function t ".
			   "  WHERE t.ParentPath LIKE '%,".$parent_id.",%' ";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
				
		DAL::Instance()->Query( $sql );
		
		$sql = " UPDATE pm_Function t SET t.SortIndex = (SELECT u.SortIndex FROM tmp_FunctionSort u WHERE u.pm_FunctionId = t.pm_FunctionId) ".
			   "  WHERE t.ParentPath LIKE '%,".$parent_id.",%' ";

		DAL::Instance()->Query( $sql );
		
		DAL::Instance()->Query( "DROP TABLE tmp_FunctionSort" );

        $className = get_class($object_it->object);

        $sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
            " SELECT NOW(), NOW(), w.VPD, w.pm_FunctionId, '" . $className . "' ".
            "     FROM pm_Function w WHERE w.ParentPath LIKE '%,".$parent_id.",%' AND ParentFeature <> ".$parent_id;

        DAL::Instance()->Query( $sql );
	} 	
}
