<?php

class SortChangeLogRecentClause extends SortClauseBase
{
    private $sortType;

    function __construct($sortType = 'DESC') {
        $this->sortType = $sortType;
    }

    function clause() {
 		return " MAX({$this->setColumnAlias('RecordCreated')}) {$this->sortType} ";
 	}
}
