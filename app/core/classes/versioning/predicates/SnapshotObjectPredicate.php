<?php

class SnapshotObjectPredicate extends FilterPredicate
{
    function _predicate( $filter )
    {
        global $model_factory;
        
        if ( $filter == 'none' ) return " AND 1 = 1 ";
        
        $snapshot = $model_factory->getObject('Snapshot');
        
        $snapshot_it = $snapshot->getExact($filter);
        
        if ( $snapshot_it->getId() == '' ) return " AND 1 = 2 ";
        
        $object = $this->getObject();
        
        return " AND EXISTS (SELECT 1 FROM cms_SnapshotItem i ".
               "              WHERE i.Snapshot = ".$snapshot_it->getId().
               "                AND i.ObjectId = t.".$object->getEntityRefName()."Id".
               "                AND i.ObjectClass = '".get_class($object)."') ";
    }
}