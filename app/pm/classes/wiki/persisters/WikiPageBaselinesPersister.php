<?php

class WikiPageBaselinesPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Baselines');
    }

    function getSelectColumns( $alias )
 	{
 		$objectPK = $this->getPK($alias);
 		return array(
            " (SELECT GROUP_CONCAT(CAST(tr.cms_SnapshotId AS CHAR)) 
                 FROM cms_Snapshot tr 
                WHERE tr.ObjectId = ".$objectPK."
                  AND tr.ObjectClass = '".get_class($this->getObject())."' 
                  AND tr.Type IS NULL ) Baselines ",
        );
 	}
}
