<?php

class WorkTableCustomerPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 	 		" ( SELECT GROUP_CONCAT(IFNULL(av.StringValue, av.TextValue))".
 	 		"	  FROM pm_AttributeValue av, pm_CustomAttribute ca ".
	 		"    WHERE av.ObjectId = ".$this->getPK($alias).
			"	   AND av.CustomAttribute = ca.pm_CustomAttributeId ".
			"	   AND ca.ReferenceName = '".CUSTOMER_ATTRIBUTE_NAME."' ) Customer ";
 		
 		return $columns;
 	}
}
