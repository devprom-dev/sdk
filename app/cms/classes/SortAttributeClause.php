<?php

class SortAttributeClause extends SortClauseBase
{
 	var $attr;
 	
 	function SortAttributeClause( $attribute )
 	{
 		$this->attr = $attribute;
 		
 		parent::SortClauseBase();
 	}
 	
 	function getAttributeName()
 	{
 	 	$parts = preg_split('/\./', $this->attr);
 	 	
		if ( count($parts) > 1 )
		{
			$attr = $parts[0];
		}
		else
		{
			$attr = $this->attr;
		}
		
		return $attr;
 	}
 	
 	function getSortType()
 	{
 	 	$parts = preg_split('/\./', $this->attr);
 	 	
		if ( count($parts) > 1 )
		{
			$sort_type = $parts[1] == 'D' ? "DESC" : "ASC"; 
		}
		else
		{
			$sort_type = "ASC";
		}
		
		return $sort_type;
 	}
 	
 	function getColumn()
 	{
 	    $object = $this->getObject();
 	    
 	    $attr = $this->getAttributeName();
 	    
 	    return $this->getAlias() != '' && $object->IsAttributeStored($attr) && $object->getAttributeOrigin($attr) == ORIGIN_METADATA 
 			? $this->getAlias().'.'.$attr : "`".$attr."`";
 	}
 	
 	function clause()
 	{
 		$object = $this->getObject();
 		
		$sort_type = $this->getSortType(); 
		
		$attr = $this->getAttributeName();

		$sql_attr = $this->getColumn();
 		
		switch ( $attr )
		{
			case 'State':
				if ( method_exists( $object, 'getStatableClassName' ) )
				{
					return " (SELECT MIN(s.OrderNum) FROM pm_State s " .
						   "   WHERE s.ObjectClass = '".$object->getStatableClassName()."' " .
						   "	 AND s.VPD = '".$object->getVpdValue()."' ".
						   "     AND s.ReferenceName = ".$sql_attr.") ".$sort_type;
				}
				
			default:
	 			if ( $object->IsReference($attr) )
	 			{ 
	 				$ref = $object->getAttributeObject($attr);
	 				
					if ( $ref instanceof Metaobject && $ref->getEntity()->get('IsDictionary') == 'Y' )
					{
						 return " (SELECT s.OrderNum FROM ".$ref->getClassName()." s WHERE s.".$ref->getClassName()."Id = ".$sql_attr.") ".$sort_type." ";
					}
	 			}
	
				return " IFNULL(".$sql_attr.", 0) ".$sort_type." ";
		}
 	}
}
