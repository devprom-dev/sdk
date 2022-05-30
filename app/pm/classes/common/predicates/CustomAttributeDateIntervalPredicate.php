<?php

class CustomAttributeDateIntervalPredicate extends FilterPredicate
{
 	private $attribute;
 	private $from;
 	private $till;

 	function __construct( $attribute, $from, $till )
 	{
 		$this->attribute = $attribute;
 		$this->from = $from;
 		$this->till = $till == '' ? $this->from : $till;
 		
 		parent::__construct( $this->from );
 	}
 	
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();
 		
 		$attr_it = getFactory()->getObject('pm_CustomAttribute')->getByAttribute($object, $this->attribute);
 		if ( $attr_it->count() < 1 ) return " AND 1 = 2";
 			
 		$value_column = $attr_it->getRef('AttributeType')->getValueColumn();

        $mapper = new ModelDataTypeMappingDate();
        $from = "'".$mapper->map(DAL::Instance()->Escape($this->from))."'";
        $till = "'".$mapper->map(DAL::Instance()->Escape($this->till))."'";

        $sql[] =
            " EXISTS (SELECT 1 FROM pm_AttributeValue av ".
            "		   WHERE av.ObjectId = t.".$object->getClassName()."Id ".
            "		   	 AND av.CustomAttribute IN (".join(',',$attr_it->idsToArray()).") ".
            "			 AND av.".$value_column." BETWEEN ".$from." AND ".$till.") ";

 		return " AND (".join(" OR ", $sql)." ) ";
 	}
}