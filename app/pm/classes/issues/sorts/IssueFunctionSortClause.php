<?php

class IssueFunctionSortClause extends SortAttributeClause
{
 	function __construct() {
 		parent::__construct('Caption');
 	}
 	
 	function clause()
 	{
        $ref_field = $this->getAlias() != '' ? $this->getAlias().'.Function' : 'Function';
		return " IFNULL((SELECT i.OrderNum FROM pm_Importance i, pm_Function f WHERE f.Importance = i.pm_ImportanceId AND f.pm_FunctionId = ".$ref_field."), 9999) ".$this->getSortType().", ".$ref_field." ".$this->getSortType();
 	}
}