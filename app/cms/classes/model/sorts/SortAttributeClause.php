<?php

class SortAttributeClause extends SortClauseBase
{
 	var $attr;
    private $valueInsteadOfNull;
    private $textInsteadOfNull;
    private $nullOnTop;
 	
 	function SortAttributeClause( $attribute )
 	{
 		$this->attr = $attribute;
        $this->setNullOnTop();
 		parent::SortClauseBase();
 	}

 	function setNullOnTop( $nullOnTop = true ) {
 	    $this->nullOnTop = $nullOnTop;
 	    if ( $nullOnTop ) {
            $this->textInsteadOfNull = '!';
            $this->valueInsteadOfNull = '0';
        }
        else {
            $this->textInsteadOfNull = 'я';
            $this->valueInsteadOfNull = '9999';
        }
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
        $attr = $this->getAttributeName();

        if ( !$object->hasAttribute($attr) ) {
            throw new Exception('There is no attribute '.$attr.' of '.get_class($object).' entity');
        }

		$sort_type = $this->getSortType();
		$sql_attr = $this->getColumn();
 		
		switch ( $attr )
		{
			case 'State':
				if ( method_exists( $object, 'getStatableClassName' ) )
				{
                    $items = \WorkflowScheme::Instance()->getNonTerminalStates($object);
                    $items[] = array_shift($items);
                    $items = array_merge(
                        $items, \WorkflowScheme::Instance()->getTerminalStates($object)
                    );
                    $index = 0;
                    $query = array_map(function($value) use(&$index) {
                        return " WHEN '".$value."' THEN ".($index++)." ";
                    }, $items);
                    return " CASE ".$sql_attr." ".join('', $query)." END ";
				}
				
			default:
	 			if ( $object->IsReference($attr) )
	 			{ 
	 				$ref = $object->getAttributeObject($attr);

					if ( $ref instanceof Metaobject && $ref->getEntity()->get('IsDictionary') == 'Y' )
					{
						$alt_sort_column = $ref->getAttributeType('Caption') != "" ? 'Caption' : $ref->getIdAttribute();
						return " IFNULL((SELECT IFNULL(s.OrderNum, s.".$alt_sort_column.") FROM ".$ref->getClassName()." s WHERE s.".$ref->getIdAttribute()." = ".$sql_attr."), ".$this->valueInsteadOfNull.") ".$sort_type." ";
					}

					if ( $ref instanceof User ) {
                        $userId = getSession()->getUserIt()->getId();
                        if ( $userId > 0 ) {
                            return " (SELECT IF(s.".$ref->getIdAttribute()." = ".$userId.", '1', IFNULL(s.Caption, '".$this->textInsteadOfNull."')) FROM ".$ref->getClassName()." s WHERE s.".$ref->getIdAttribute()." = ".$sql_attr.") ".$sort_type." ";
                        }
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
                            $sort->setNullOnTop($this->nullOnTop);
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
	 				$sql_attr = " IFNULL(".$sql_attr.", ".$this->valueInsteadOfNull.") ";
	 			}

			 	if ( in_array($object->getAttributeType($attr), array('varchar','text','largetext')) ) { 
	 				$sql_attr = " IFNULL(".$sql_attr.", '".$this->textInsteadOfNull."') ";
	 			}

				return $sql_attr." ".$sort_type." ";
		}
 	}
}
