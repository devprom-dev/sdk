<?php

class WorkItemExactTypeStatePredicate extends FilterPredicate
{
    private $object = null;

    function __construct($filter, $object) {
        parent::__construct($filter);
        $this->object = $object;
    }

 	function _predicate( $filter )
 	{
 	    $statePredicate = new StatePredicate( $filter );
        $statePredicate->setObject($this->object);
        return "AND (t.ObjectClass = '".get_class($this->object)."' " .$statePredicate->getPredicate()." OR t.ObjectClass <> '".get_class($this->object)."')";
 	}
}