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
						   "	 AND s.VPD IN ('".join("','",$object->getVpds())."') ".
						   "     AND s.ReferenceName = ".$sql_attr.") ".$sort_type;
				}
				
			default:
	 			if ( $object->IsReference($attr) )
	 			{ 
	 				$ref = $object->getAttributeObject($attr);
	 				
					if ( $ref instanceof Metaobject && $ref->getEntity()->get('IsDictionary') == 'Y' )
					{
						$alt_sort_column = $ref->getAttributeType('Caption') != "" ? 'Caption' : $ref->getIdAttribute();
						return " (SELECT IFNULL(s.OrderNum, s.".$alt_sort_column.") FROM ".$ref->getClassName()." s WHERE s.".$ref->getIdAttribute()." = ".$sql_attr.") ".$sort_type." ";
					}
					
					$sorts = $ref->getSortDefault();
					if ( count($sorts) > 0 && $ref->getEntityRefName() != $object->getEntityRefName() )
					{
						$clause = array_shift($sorts);
						if ( $clause instanceof SortAttributeClause )
						{
			 				if ( in_array($object->getAttributeType($clause->getAttributeName()), array('varchar','text','largetext')) ) { 
 								$default_val = "'!'";
	 						} else {
	 							$default_val = "0";
	 						}
							$sort_clause = " (SELECT IFNULL(s.".$clause->getAttributeName().", ".$default_val.") FROM ".$ref->getClassName()." s WHERE s.".$ref->getIdAttribute()." = ".$sql_attr.") ".$sort_type;
							$alt_sort_column = $ref->getAttributeType('Caption') != "" ? 'Caption' : $ref->getIdAttribute();
							return $sort_clause.", (SELECT s.".$alt_sort_column." FROM ".$ref->getClassName()." s WHERE s.".$ref->getIdAttribute()." = ".$sql_attr.") ".$sort_type." ";
						}
					}
	 			}

	 			if ( in_array($object->getAttributeType($attr), array('integer','float')) ) { 
	 				$sql_attr = " IFNULL(".$sql_attr.", 0) ";
	 			}

			 	if ( in_array($object->getAttributeType($attr), array('varchar','text','largetext')) ) { 
	 				$sql_attr = " IFNULL(".$sql_attr.", '!') ";
	 			}
	 			
				return $sql_attr." ".$sort_type." ";
		}
 	}
}
