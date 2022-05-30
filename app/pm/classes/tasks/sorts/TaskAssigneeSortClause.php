<?php

class TaskAssigneeSortClause extends SortAttributeClause
{
 	function __construct() {
 		parent::__construct('Caption');
 	}
 	
 	function clause() {
        $ref_field = $this->getAlias() != '' ? $this->getAlias().'.Assignee' : 'Assignee';
		return " (SELECT s.Caption FROM cms_User s WHERE s.cms_UserId = ".$ref_field.") ".$this->getSortType()." ";
 	}
}