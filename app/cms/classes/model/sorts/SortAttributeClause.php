<?php

class SortAttributeClause extends SortClauseBase
{
 	var $attr;
    private $valueInsteadOfNull;
    private $textInsteadOfNull;
    private $dateInsteadOfNull;
    private $nullOnTop;
    private $referenceSorts = array();
 	
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
            $this->dateInsteadOfNull = '0001-01-01';
        }
        else {
            $this->textInsteadOfNull = 'я';
            $this->valueInsteadOfNull = '9999';
            $this->dateInsteadOfNull = '3001-01-01';
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

 	function setReferenceSorts( $referenceAttribute, $sorts = array() ) {
        $this->referenceSorts[$referenceAttribute] = $sorts;
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
            return "";
        }

		$sort_type = $this->getSortType();
		$sql_attr = $this->getColumn();
 		
		switch ( $attr )
		{
            case 'UID':
                return " ".$this->getAlias().".".$this->getObject()->getIdAttribute()." ".$sort_type;

			case 'State':
			    if ( $object instanceof MetaobjectStatable ) {
                    return " IFNULL((SELECT s.OrderNum FROM pm_State s WHERE s.ReferenceName = ".$sql_attr." AND s.VPD = t.VPD AND s.ObjectClass = '".strtolower(get_class($object))."'), ".$this->valueInsteadOfNull.") ".$sort_type." ";
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

					$sorts = is_array($this->referenceSorts[$attr]) ? $this->referenceSorts[$attr] : $ref->getSortDefault();
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
						    if ( $sort instanceof SortKeyClause ) {
						        return $sql_attr . " " . $sort_type;
                            }
							if ( !$sort instanceof SortAttributeClause ) continue;
                            $sort->setObject($ref);
                            $sort->setAlias('t');
                            $sort->setNullOnTop($this->nullOnTop);

							$clause = trim($sort->clause());
							if ( $clause == '' ) continue;
							if ( strpos($clause, 'SELECT') === false ) {
                                $sort->setAlias('s');
								$clause = preg_replace('/\s(ASC|DESC)\s/i', '', $sort->clause());
								$sort_clauses[] = " (SELECT ".$clause." FROM ".$ref->getClassName()." s WHERE s.".$ref->getIdAttribute()." = ".$sql_attr.") ".$sort->getSortType();
							}
							else {
                                $clause = str_replace('FROM ', 'FROM ' . $ref->getClassName()." e, ", $clause);
                                $clause = str_replace('t.', 'e.', $clause);
                                $clause = str_replace('WHERE ', 'WHERE e.' . $ref->getIdAttribute()." = ".$sql_attr . " AND ", $clause);
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

                if ( in_array($object->getAttributeType($attr), array('date','datetime')) ) {
                    $sql_attr = " IFNULL(".$sql_attr.", '".$this->dateInsteadOfNull."') ";
                }

                return $sql_attr." ".$sort_type." ";
		}
 	}
}
