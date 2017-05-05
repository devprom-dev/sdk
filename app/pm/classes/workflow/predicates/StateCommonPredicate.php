<?php

class StateCommonPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        if ( !$this->getObject() instanceof MetaobjectStatable ) return " AND 1 = 1 ";

        $states = array();
        foreach( preg_split('/,/', $filter) as $state ) {
            switch( $state )
            {
                case StateCommonRegistry::Submitted:
                    $states[] = array_shift(WorkflowScheme::Instance()->getStates($this->getObject()));
                    break;
                case StateCommonRegistry::Progress:
                    $objectStates = WorkflowScheme::Instance()->getNonTerminalStates($this->getObject());
                    array_shift($objectStates);
                    $states = array_merge($objectStates, $states);
                    break;
                case StateCommonRegistry::Done:
                    $states = array_merge(WorkflowScheme::Instance()->getTerminalStates($this->getObject()), $states);
                    break;
            }
        }

        if ( count($states) < 1 ) return " AND 1 = 1 ";
        return " AND ".$this->getAlias().".State IN ('".join("','", $states)."') ";
    }
}