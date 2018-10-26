<?php

include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMapper.php";

class FilterAttributePredicate extends FilterPredicate
{
 	private $attr = '';
 	
 	private $has_null = false;
 	
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
 	
 	function setNullValue( $flag )
 	{
 		$this->has_null = $flag;
 	}
 	
 	function hasNullValue()
 	{
 		return $this->has_null;
 	}
 	
 	function setIds( $array )
 	{
 		$this->ids = $array;
 	}
 	
 	function getIds()
 	{
 		return $this->ids;
 	}
 	
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();

 		if ( $object->getAttributeDbType($this->attr) == '' ) {
 			$this->setIds(array('-1'));
 			return $this->getQueryPredicate();
 		}

 		if ( !$this->getObject() instanceof User && is_object(getSession()) ) {
            $filter = preg_replace('/user-id/', getSession()->getUserIt()->getId(), $filter);
        }
        $has_null_value = strpos($filter, 'none') !== false;

 		if ( $this->hasMultipleValues() && $object->IsReference($this->attr) ) {
            $values = \TextUtils::parseIds($filter);
            if ( count($values) < 1 ) {
                $values = \TextUtils::parseItems($filter);
            }
            elseif ( $has_null_value ) {
                $values[] = 0;
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

 		$this->setNullValue($has_null_value);
 		
 		if ( $object->IsReference( $this->attr ) )
 		{
 			$ref = $object->getAttributeObject( $this->attr );
 			if ( $ref instanceof CacheableSet ) {
                foreach( $values as $key => $value ) {
                    $values[$key] = $object->formatValueForDb($this->attr, addslashes($value));
                }
            }
            else {
                $registry = $ref->getRegistry();
                $registry->setPersisters(array());

                if ( $is_numeric_value || $ref->getAttributeDbType('ReferenceName') == '' ) {
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
                        new FilterAttributePredicate('ReferenceName', $values),
                        new FilterVpdPredicate($object->getVpds())
                    ));
                    $values = $ref_it->idsToArray();
                }
                else {
                    foreach( $values as $key => $value ) {
                        $values[$key] = $object->formatValueForDb($this->attr, addslashes($value));
                    }
                }
            }
 		}
 		else
 		{
 			foreach( $values as $key => $value ) {
 				$values[$key] = $object->formatValueForDb($this->attr, addslashes($value));
 			}
 		}

 		if ( count($values) < 1 ) $values = array(0);

 		$this->setIds($values);

 		return $this->getQueryPredicate();
 	}
 	
 	function getQueryPredicate()
 	{
		$field = $this->getAlias().".".$this->getAttribute();
 	 	if ( $this->hasNullValue() ) {
 			return " AND (".$field." IN (".join($this->getIds(),',').") OR ".$this->getAlias().".".$this->getAttribute()." IS NULL )";
 		}
 		else {
 			return " AND ".$field." IN (".join($this->getIds(),',').") ";
 		}
 	}
}