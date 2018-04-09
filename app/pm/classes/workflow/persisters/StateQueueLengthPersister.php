<?php

class StateQueueLengthPersister extends ObjectSQLPersister
{
    private $vpds = array();

    function __construct(array $attributes, array $vpds) {
        parent::__construct($attributes);
        $this->vpds = $vpds;
    }

    function getSelectColumns( $alias )
 	{
 	    return array(
            "( SELECT SUM(s.QueueLength) FROM pm_State s ".
            "   WHERE s.ReferenceName = ".$alias.".ReferenceName ".
            "     AND s.VPD IN ('".join("','", $this->vpds)."') ".
            "     AND s.ObjectClass = ".$alias.".ObjectClass) QueueLength "
        );
 	}
}