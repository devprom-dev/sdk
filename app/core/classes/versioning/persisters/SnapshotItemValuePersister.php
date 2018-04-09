<?php

include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class SnapshotItemValuePersister extends ObjectSQLPersister
{
    var $snapshot_it;
    
    function __construct( $baseline )
    {
        global $model_factory;
        
        $snapshot = $model_factory->getObject('Snapshot');
        
        $this->snapshot_it = $snapshot->getExact( $baseline );
    }
    
    function getSelectColumns( $alias )
    {
        if ( $this->snapshot_it->getId() == '' ) return array();
        
        $columns = array();
        
        $versioned = new VersionedObject();
        
        $versioned_it = $versioned->getExact(get_class($this->getObject()));
        
        if ( $versioned_it->getId() == '' ) return $columns;
        
        $attributes = $versioned_it->get('Attributes');
        
        foreach( $attributes as $attribute )
        {
            $columns[] = 
                " (SELECT ivl.Value FROM cms_SnapshotItemValue ivl, cms_SnapshotItem itm " .
    			"   WHERE ivl.SnapshotItem = itm.cms_SnapshotItemId " .
    			"     AND ivl.ReferenceName = '".$attribute."' ".
    			"     AND itm.Snapshot = ".$this->snapshot_it->getId().
                "     AND itm.ObjectId = ".$this->getPK($alias).
                "     AND itm.ObjectClass = '".$versioned_it->getId()."') ".$attribute." ";
        }

        return $columns;
    }
}
