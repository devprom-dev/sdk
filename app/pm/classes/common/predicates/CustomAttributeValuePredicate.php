<?php

class CustomAttributeValuePredicate extends FilterPredicate
{
 	var $attribute;
 	
 	function __construct( $attribute, $value )
 	{
 		$this->attribute = $attribute;
 		
 		parent::__construct( $value );
 	}
 	
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();
 		
 		$attr_it = getFactory()->getObject('pm_CustomAttribute')->getByAttribute($object, $this->attribute);
 		if ( $attr_it->count() < 1 ) return " AND 1 = 2";
 			
 		$mapper = new ModelDataTypeMapper();
 		$value_column = $attr_it->getRef('AttributeType')->getValueColumn();

 		$sql = array();
        foreach( preg_split('/,/',$filter) as $value )
        {
            if ( $value == 'none' ) {
                $sql[] =
                    " NOT EXISTS (SELECT 1 FROM pm_AttributeValue av ".
                    "              WHERE av.ObjectId = t.".$object->getClassName()."Id ".
                    "            	 AND av.CustomAttribute IN (".join(',',$attr_it->idsToArray()).") )";
                $sql[] =
                    " EXISTS (SELECT 1 FROM pm_AttributeValue av ".
                    "          WHERE av.ObjectId = t.".$object->getClassName()."Id ".
                    "			 AND av.".$value_column." IS NULL".
                    "			 AND av.CustomAttribute IN (".join(',',$attr_it->idsToArray()).") )";
            }
            else {
                $data = array( $this->attribute => $value );
                $mapper->map( $object, $data );
                $values[] = $object->formatValueForDB($this->attribute, $data[$this->attribute]);
            }
        }

        if ( count($values) > 0 ) {
            if ( in_array('multiselect', $object->getAttributeGroups($this->attribute)) ) {
                foreach($values as $value) {
                    $sql[] =
                        " EXISTS (SELECT 1 FROM pm_AttributeValue av ".
                        "		   WHERE av.ObjectId = t.".$object->getClassName()."Id ".
                        "		   	 AND av.CustomAttribute IN (".join(',',$attr_it->idsToArray()).") ".
                        "			 AND FIND_IN_SET(".$value.", av.".$value_column.") > 0 ) ";
                }
            }
            else {
                $sql[] =
                    " EXISTS (SELECT 1 FROM pm_AttributeValue av ".
                    "		   WHERE av.ObjectId = t.".$object->getClassName()."Id ".
                    "		   	 AND av.CustomAttribute IN (".join(',',$attr_it->idsToArray()).") ".
                    "			 AND av.".$value_column." IN (".join(",",$values).") ) ";
            }
        }

 		return " AND (".join(" OR ", $sql)." ) ";
 	}
}