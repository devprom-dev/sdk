<?php

class GroupAttributeClause extends SortClauseBase
{
 	var $attr;
 	
 	function GroupAttributeClause( $attribute )
 	{
 		$this->attr = $attribute;
 		
 		parent::SortClauseBase();
 	}
 	
 	function clause()
 	{
 		$object = $this->getObject();
 		
		$attr = $this->attr;

		$sql_attr = $this->getAlias() != '' && $object->IsAttributeStored($attr) && $object->getAttributeOrigin($attr) == ORIGIN_METADATA 
 			? $this->getAlias().'.'.$attr : $attr;
 		
		switch ( $this->attr )
		{
			case 'State':
				if ( method_exists( $object, 'getStatableClassName' ) )
				{
 					$vpd_attr = $this->getAlias() != '' ? $this->getAlias().'.VPD' : 't.VPD';
 					
					return " (SELECT s.OrderNum FROM pm_State s " .
						   "   WHERE s.ObjectClass = '".$object->getStatableClassName()."' " .
						   "     AND s.ReferenceName = ".$sql_attr."" .
						   "	 AND s.VPD = ".$vpd_attr.") ";
				}
				
			default:
	 			if ( $object->IsReference($attr) )
	 			{ 
	 				$ref = $object->getAttributeObject($attr);
					if ( $ref->IsDictionary() ) {
						 return " (SELECT s.OrderNum FROM ".$ref->getClassName()." s WHERE s.".$ref->getClassName()."Id = ".$sql_attr.") ";
					}
	 			}
	
				return $sql_attr;
		}
 	}
}