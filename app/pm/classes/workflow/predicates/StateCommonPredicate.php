<?php

class StateCommonPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        if ( !$this->getObject() instanceof MetaobjectStatable ) return " AND 1 = 1 ";

        $states = array_filter(preg_split('/,/', $filter), function($value) {
            return in_array($value, array(
                StateCommonRegistry::Submitted,
                StateCommonRegistry::Progress,
                StateCommonRegistry::Done
            ));
        });

        if ( count($states) < 1 ) return " AND 1 = 1 ";

        return " AND EXISTS ( SELECT 1 FROM pm_State s 
                    WHERE ".$this->getAlias().".VPD = s.VPD 
                      AND ".$this->getAlias().".State = s.ReferenceName
                      AND s.ObjectClass = '".get_class($this->getObject())."'
                      AND s.IsTerminal IN ('".join("','", $states)."')) ";
    }
}