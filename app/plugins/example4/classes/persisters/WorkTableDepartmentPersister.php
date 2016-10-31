<?php

class WorkTableDepartmentPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$object = new WorkTableDepartment();
 		
 		$iterator = $object->getAll();
 		
 		$case = array();
 		
 		while( !$iterator->end() )
 		{
 			$case[] = " WHEN ".$iterator->getId()." THEN '".$iterator->get('Caption')."' ";
 			
 			$iterator->moveNext();
 		}
 		
 		if ( count($case) < 1 ) $case[] = " WHEN 0 THEN '' ";
 		
 		$columns[] = 
 	 		" ( SELECT GROUP_CONCAT((CASE av.IntegerValue ".join("", $case)." END))".
 	 		"	  FROM pm_AttributeValue av, pm_CustomAttribute ca ".
	 		"    WHERE av.ObjectId = ".$this->getPK($alias).
			"	   AND av.CustomAttribute = ca.pm_CustomAttributeId ".
			"	   AND ca.ReferenceName = '".DEPT_ATTRIBUTE_NAME."' ) Department ";
 		
 		return $columns;
 	}
}
