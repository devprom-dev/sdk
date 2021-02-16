<?php

class WikiPageFeaturePersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('Feature');
	}

	function getSelectColumns( $alias )
 	{
 		return array (
			" (SELECT GROUP_CONCAT(DISTINCT CAST(r.Feature AS CHAR)) ".
            "      FROM pm_FunctionTrace r " .
            "  	  WHERE r.ObjectId = ".$this->getPK($alias).
            "       AND r.ObjectClass IN ('Requirement','TestScenario','HelpPage') ) Feature "
		);
 	}

 	function add( $object_id, $parms ) {
        if ( $parms['Feature'] != '' ) {
            $this->applyFeature($object_id, $parms['Feature']);
        }
    }

 	function modify($object_id, $parms) {
        if ( $parms['Feature'] != '' ) {
            $this->applyFeature($object_id, $parms['Feature']);
        }
    }

    function applyFeature( $object_id, $featureId )
    {
        $registry = getFactory()->getObject('pm_FunctionTrace')->getRegistry();
        $trace_it = $registry->Query(
            array (
                new FilterAttributePredicate('ObjectId', $object_id),
                new FilterAttributePredicate('ObjectClass', get_class($this->getObject()))
            )
        );
        while( !$trace_it->end() ) {
            $registry->Delete($trace_it);
            $trace_it->moveNext();
        }
        $registry->Create(
            array(
                'ObjectId' => $object_id,
                'ObjectClass' => get_class($this->getObject()),
                'Feature' => $featureId
            )
        );
    }
}
