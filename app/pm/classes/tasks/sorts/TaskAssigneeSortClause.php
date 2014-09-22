<?php

class TaskAssigneeSortClause extends SortAttributeClause
{
 	function __construct()
 	{
 		parent::__construct('Caption');
 	}
 	
 	function clause()
 	{
        $ref_field = $this->getAlias() != '' ? $this->getAlias().'.Assignee' : 'Assignee';
 	      
		return " (SELECT s.Caption FROM pm_Participant s WHERE s.pm_ParticipantId = ".$ref_field.") ".$this->getSortType()." ";
 	}
}