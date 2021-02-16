<?php

include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class SnapshotItemValuePersister extends ObjectSQLPersister
{
    var $snapshot_it;
    
    function __construct( $baseline ) {
        $this->snapshot_it = getFactory()->getObject('Snapshot')->getExact( $baseline );
    }
    
    function getSelectColumns( $alias )
    {
        if ( $this->snapshot_it->getId() == '' ) return array();

        $versioned = new VersionedObject();
        $versioned_it = $versioned->getExact(get_class($this->getObject()));

        $columns = array();
        if ( $versioned_it->getId() == '' ) return $columns;

        foreach( $versioned_it->get('Attributes') as $attribute ) {
            $columns[] = 
                " (SELECT MAX(ivl.Value) FROM cms_SnapshotItemValue ivl, cms_SnapshotItem itm " .
    			"   WHERE ivl.SnapshotItem = itm.cms_SnapshotItemId " .
    			"     AND ivl.ReferenceName = '".$attribute."' ".
    			"     AND itm.Snapshot = ".$this->snapshot_it->getId().
                "     AND itm.ObjectId = ".$this->getPK($alias).
                "     AND itm.ObjectClass = '".$versioned_it->getId()."') ".$attribute." ";
        }

        return $columns;
    }
}
