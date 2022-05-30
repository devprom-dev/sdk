<?php

class SortIndexClause extends SortClauseBase
{
    private $direction = '';

    function __construct( $direction = 'ASC' ) {
        $this->direction = $direction;
    }

 	function clause() {
 		return " SortIndex {$this->direction} ";
 	}
}
