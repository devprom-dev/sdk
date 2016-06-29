<?php

class WorkTableDepartmentPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( strpos($filter, 'none') !== false )
 		{
			return " AND NOT EXISTS (SELECT 1 FROM pm_AttributeValue av, pm_CustomAttribute ca ".
	 			   "			  WHERE av.ObjectId = t.".$this->getObject()->getClassName()."Id ".
				   "			    AND av.CustomAttribute = ca.pm_CustomAttributeId ".
				   "				AND ca.ReferenceName = '".DEPT_ATTRIBUTE_NAME."' ".
				   "				AND av.IntegerValue IS NOT NULL ) ";
 		}
 		else
 		{
			return " AND EXISTS (SELECT 1 FROM pm_AttributeValue av, pm_CustomAttribute ca ".
	 			   "			  WHERE av.ObjectId = t.".$this->getObject()->getClassName()."Id ".
				   "			    AND av.CustomAttribute = ca.pm_CustomAttributeId ".
				   "				AND ca.ReferenceName = '".DEPT_ATTRIBUTE_NAME."' ".
				   "				AND av.IntegerValue IN (".$filter.") ) ";
 		}
 	}
}