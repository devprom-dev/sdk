<?php

class FilterAttributePredicate extends FilterPredicate
{
 	private $attr = '';
 	private $referenceSearchField = 'ReferenceName';
 	private $ids = array();
 	private $has_multiple_values = true;
 	
 	function __construct( $attr, $filter )
 	{
 		$this->attr = $attr;
 		parent::__construct( $filter );
 	}
 	
 	function getAttribute()
 	{
 		return $this->attr;
 	}
 	
 	function setHasMultipleValues( $flag )
 	{
 		$this->has_multiple_values = $flag;
 	}
 	
 	function hasMultipleValues()
 	{
 		return $this->has_multiple_values;
 	}
 	
 	function setIds( $array )
 	{
 		$this->ids = $array;
 	}
 	
 	function getIds()
 	{
 		return $this->ids;
 	}

 	function setReferenceSearchAttribute( $attribute ) {
 	    $this->referenceSearchField = $attribute;
    }

    function check( $filter )
    {
        if ( is_array($filter) ) {
            if ( count($filter) < 1 ) return $filter;

            array_walk( $filter, function (&$value, $key) {
                return $value = addslashes($value);
            });
            return join(',', $filter);
        }
        else if ( is_object($filter) ) {
            return $filter;
        }
        else {
            return addslashes($filter);
        }
    }

 	function _predicate( $filter )
 	{
 		$object = $this->getObject();

 		if ( $object->getAttributeDbType($this->attr) == '' ) {
 			return " AND 1 = 2 ";
 		}

 		if ( $this->hasMultipleValues() && $object->IsReference($this->attr) ) {
            $values = \TextUtils::parseIds($filter);
            if ( count($values) < 1 ) {
                $values = \TextUtils::parseItems($filter);
            }
 		}
 		else {
 			$values = \TextUtils::parseItems($filter);
 		}

 		$is_numeric_value = false;
 		foreach( $values as $key => $value ) {
 			if ( is_numeric($value) ) { $is_numeric_value = true; break; }
 		}
 			
 		foreach( $values as $key => $value ) {
 			if ( $value == 'none' ) {
 				$values[$key] = $is_numeric_value ? 0 : 'NULL';
 				break;
 			}
 		}

 		if ( $object->IsReference( $this->attr ) ) {
 			$ref = $object->getAttributeObject( $this->attr );
 			if ( $ref instanceof CacheableSet ) {
                foreach( $values as $key => $value ) {
                    $values[$key] = $object->formatValueForDb($this->attr, $value);
                }
            }
            else {
                $registry = $ref->getRegistry();
                $registry->setPersisters(array());

                if ( $is_numeric_value || $ref->getAttributeDbType($this->referenceSearchField) == '' ) {
                    $values = array_filter($values, function( $val ) {
                        return is_numeric($val);
                    });
                    if ( count($values) > 0 ) {
                        $ref_it = $registry->Query(
                            array (
                                new FilterInPredicate($values)
                            )
                        );
                    }
                    else {
                        $ref_it = $ref->getEmptyIterator();
                    }
                    $values = $ref_it->idsToArray();
                }
                elseif ( $ref->getEntityRefName() != 'entity' ) {
                    $ref_it = $registry->Query( array (
                        new FilterAttributePredicate($this->referenceSearchField, $values),
                        new FilterVpdPredicate($object->getVpds())
                    ));
                    $values = $ref_it->idsToArray();
                }
                else {
                    foreach( $values as $key => $value ) {
                        $values[$key] = $object->formatValueForDb($this->attr, $value);
                    }
                }
            }
 		}
 		else {
 			foreach( $values as $key => $value ) {
 				$values[$key] = $object->formatValueForDb($this->attr, $value);
 			}
 		}

        $sqls = array();

 		if ( count($values) > 0 ) {
            $this->setIds($values);
            $sqls[] = $this->getQueryPredicate();
        }
 		if ( $this->hasAny($filter) ) {
            $sqls[] = $this->getAlias().".".$this->getAttribute()." IS NOT NULL ";
        }
        if ( $this->hasNone($filter) ) {
            $sqls[] = $this->getAlias().".".$this->getAttribute()." IS NULL ";
        }

 		return count($sqls) < 1 ? " AND 1 = 2 " : " AND (".join(" OR ", $sqls).") ";
 	}
 	
 	function getQueryPredicate()
 	{
		$field = $this->getAlias().".".$this->getAttribute();
		if ( in_array('multiselect', $this->getObject()->getAttributeGroups($this->getAttribute())) ) {
            $sql = array();
            foreach($this->getIds() as $value) {
                $sql[] = " FIND_IN_SET(".$value.", ".$field.") > 0  ";
            }
            return join(' OR ', $sql);
        }
        return " ".$field." IN (".join($this->getIds(),',').") ";
 	}
}