<?php

class WikiPageCurrentBaselinePersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('CurrentBaseline');
    }

    function getSelectColumns( $alias )
 	{
 		return array(
            " (SELECT GROUP_CONCAT(CAST(tr.cms_SnapshotId AS CHAR)) 
                 FROM cms_Snapshot tr 
                WHERE tr.ObjectId = t.DocumentId
                  AND tr.ObjectClass = '".get_class($this->getObject())."' 
                  AND tr.Type IS NOT NULL LIMIT 1) CurrentBaseline ",
        );
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
