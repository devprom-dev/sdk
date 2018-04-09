<?php

class WorkItemStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$map = array(
		    'initial' => 'N',
            'progress' => 'I',
            'final' => 'Y'
        );
		$values = array();
		foreach( preg_split('/[-,]/', $filter) as $value ) {
		    if ( $map[$value] == '' ) continue;
            $values[] = $map[$value];
        }

		if ( count($values) > 0 ) {
			return " AND ".$this->getAlias().".IsTerminal IN ('".join($values,"','")."')";
		}
		else {
			return " AND 1 = 2 ";
		}
 	}
}