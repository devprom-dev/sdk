<?php

class WikiPageChangeYounger extends FilterPredicate
{
 	function _predicate( $filter )
    {
 	    if ( !is_object($filter) ) return " AND 1 = 2 ";

        $ids = array();
 	    $stateIt = \WorkflowScheme::Instance()->getStateIt($filter);
 	    while( !$stateIt->end() ) {
 	        if ( $stateIt->get('IsTerminal') == 'Y' ) {
                $ids[] = $stateIt->getId();
            }
            $stateIt->moveNext();
        }
        if ( count($ids) < 1 ) $ids = array(0);

        return " AND t.RecordCreated > 
            (SELECT MAX(so.RecordCreated) FROM pm_StateObject so, pm_Transition tr
              WHERE so.ObjectId = ".$filter->getId()."
                AND so.ObjectClass = '".get_class($filter->object)."'
                AND so.Transition = tr.pm_TransitionId
                AND tr.SourceState IN (".join(',',$ids).")) ";
 	}
}
 