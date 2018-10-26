<?php

class ObjectTemplatePersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();
        
        foreach( $this->getObject()->getAttributesTemplated() as $attribute )
        {
            $columns[] = 
                " (SELECT MAX(ivl.Value) FROM cms_SnapshotItemValue ivl, cms_SnapshotItem itm " .
    			"   WHERE ivl.SnapshotItem = itm.cms_SnapshotItemId " .
    			"     AND ivl.ReferenceName = '".$attribute."' ".
    			"     AND itm.Snapshot = ".$this->getPK($alias).") ".$attribute." ";
        }

        return $columns;
    }
}
