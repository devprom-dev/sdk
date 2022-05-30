<?php

class SortChangeLogRecentProjectClause extends SortClauseBase
{
    private $sortType;

    function __construct($sortType = 'DESC') {
        $this->sortType = $sortType;
    }

    function clause() {
        return " (SELECT MAX(ll.RecordCreated) FROM ObjectChangeLog ll WHERE ll.VPD = t.VPD) {$this->sortType} ";
 	}
}
