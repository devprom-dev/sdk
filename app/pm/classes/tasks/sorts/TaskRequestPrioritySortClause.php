<?php

class TaskRequestPrioritySortClause extends SortAttributeClause
{
 	function __construct()
 	{
 		parent::__construct('Priority');
 	}
 	
 	function clause()
 	{
        $ref_field = $this->getAlias() != '' ? $this->getAlias().'.ChangeRequest' : 'ChangeRequest';
 	      
		return " (SELECT p.OrderNum FROM Priority p, pm_ChangeRequest s WHERE p.PriorityId = s.Priority AND s.pm_ChangeRequestId = ".$ref_field.") ".$this->getSortType()." ";
 	}
}