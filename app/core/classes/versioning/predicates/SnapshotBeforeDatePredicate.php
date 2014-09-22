<?php

class SnapshotBeforeDatePredicate extends FilterPredicate
{
    function _predicate( $filter )
    {
        global $model_factory;
        
        if ( $filter == 'none' ) return " AND 1 = 1 ";
        
        $snapshot = $model_factory->getObject('Snapshot');
        
        $snapshot_it = $snapshot->getExact($filter);
        
        if ( $snapshot_it->getId() == '' ) return " AND 1 = 2 ";
        
        $object = $this->getObject();
        
        return " AND t.RecordCreated <= TIMESTAMP('".$snapshot_it->get_native('RecordCreated')."') ";
    }
}