<?php

class IssueUnifiedTypeSortClause extends SortAttributeClause
{
    function __construct() {
        parent::__construct('Type');
    }

 	function clause() {
		return " IFNULL((SELECT p.ReferenceName FROM pm_IssueType p WHERE p.pm_IssueTypeId = ".$this->getAlias().".Type), 'z') ".$this->getSortType();
 	}
}
