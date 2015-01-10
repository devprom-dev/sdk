<?php

class TaskRequestOrderSortClause extends SortAttributeClause
{
 	function __construct()
 	{
 		parent::__construct('OrderNum');
 	}
 	
 	function clause()
 	{
        $ref_field = $this->getAlias() != '' ? $this->getAlias().'.ChangeRequest' : 'ChangeRequest';
 	      
		return " (SELECT s.OrderNum FROM pm_ChangeRequest s WHERE s.pm_ChangeRequestId = ".$ref_field.") ".$this->getSortType()." ";
 	}
}