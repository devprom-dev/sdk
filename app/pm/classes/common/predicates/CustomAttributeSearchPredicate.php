<?php

class CustomAttributeSearchPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$clauses = array();
		foreach( SearchRules::getSearchItems($filter, getSession()->getLanguageUid()) as $word ) {
			$clauses[] = " av.StringValue LIKE '%".$word."%' OR av.TextValue LIKE '%".$word."%' ";
		}
		if ( count($clauses) < 1 ) return " AND 1 = 2 ";

 		return " AND EXISTS (SELECT 1 FROM pm_CustomAttribute at, pm_AttributeValue av
 		 				      WHERE at.EntityReferenceName = '".strtolower(get_class($this->getObject()))."'
 		 				        AND at.pm_CustomAttributeId = av.CustomAttribute
 		 				        AND av.ObjectId = t.".$this->getObject()->getIdAttribute()."
 		 				        AND (".join(" OR ", $clauses).") ) ";
 	}
}
