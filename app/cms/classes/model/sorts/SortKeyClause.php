<?php

class SortKeyClause extends SortClauseBase
{
    private $direction;

    function __construct( $direction = 'ASC' ) {
        $this->direction = $direction;
    }

    function getDirection() {
        return $this->direction;
    }

 	function clause() {
 		return " {$this->getObject()->getIdAttribute()} {$this->direction} ";
 	}
}
