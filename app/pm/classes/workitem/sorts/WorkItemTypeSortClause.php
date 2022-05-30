<?php

class WorkItemTypeSortClause extends SortAttributeClause
{
 	function __construct() {
 		parent::__construct('Caption');
 	}
 	
 	function clause() {
        return " {$this->setColumnAlias('TaskType')} {$this->getSortType()}";
 	}
}