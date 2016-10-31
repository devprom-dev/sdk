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
		if ( !$object->hasAttribute($attr) ) {
			$attr = $object->getIdAttribute();
		}

		$sql_attr = $this->getColumn();
 		
		switch ( $attr )
		{
			case 'State':
				if ( method_exists( $object, 'getStatableClassName' ) )
				{
					return " (SELECT MIN(s.OrderNum) FROM pm_State s " .
						   "   WHERE s.ObjectClass = '".$object->getStatableClassName()."' " .
						   "	 AND s.VPD = ".$this->getAlias().".VPD".
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
						$titleSortFound = false;
						foreach( $sorts as $sort ) {
							if ( $sort instanceof SortAttributeClause && $sort->getAttributeName() == 'Caption' ) {
								$titleSortFound = true;
								break;
							}
						}
						if ( !$titleSortFound && $ref->IsAttributeStored('Caption') ) {
							$sort = new SortAttributeClause('Caption');
							$sort->setObject($ref);
							$sorts[] = $sort;
						}

						$sort_clauses = array();
						foreach( $sorts as $sort ) {
							if ( !$sort instanceof SortAttributeClause ) continue;
							$clause = $sort->clause();
							if ( strpos($clause, 'SELECT') === false ) {
								$sort->setAlias('s');
								$clause = preg_replace('/\s(ASC|DESC)\s/i', '', $sort->clause());
								$sort_clauses[] = " (SELECT ".$clause." FROM ".$ref->getClassName()." s WHERE s.".$ref->getIdAttribute()." = ".$sql_attr.") ".$sort_type;
							}
							else {
								$sort_clauses[] = $clause;
							}
						}
						return join(',',$sort_clauses);
					}
	 			}

				if ( in_array($object->getAttributeType($attr), array('image','file')) ) {
					$sql_attr = " '-' ";
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
