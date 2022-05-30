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
		foreach( \TextUtils::parseItems($filter) as $value ) {
		    if ( $map[$value] == '' ) continue;
            $values[] = $map[$value];
        }

        $predicates = array();
        if ( in_array('N', $values) ) {
            $predicates[] = $this->getAlias().".StartDate IS NULL AND ".$this->getAlias().".FinishDate IS NULL";
        }
        if ( in_array('I', $values) ) {
            $predicates[] = $this->getAlias().".StartDate IS NOT NULL AND ".$this->getAlias().".FinishDate IS NULL";
        }
        if ( in_array('Y', $values) ) {
            $predicates[] = $this->getAlias().".FinishDate IS NOT NULL";
        }

		if ( count($predicates) > 0 ) {
			return " AND (".join(" OR ", $predicates).")";
		}
		else {
			return " AND 1 = 2 ";
		}
 	}
}