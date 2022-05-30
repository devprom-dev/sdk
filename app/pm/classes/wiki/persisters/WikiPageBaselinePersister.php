<?php

class WikiPageBaselinePersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        return array(
            " (SELECT IF(t.Type = 'branch', CONCAT('document:',t.ObjectId), t.cms_SnapshotId)) CompareToSnapshotId "
        );
    }

    function IsPersisterImportant() {
        return true;
    }
}
