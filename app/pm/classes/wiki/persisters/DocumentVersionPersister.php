<?php

class DocumentVersionPersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();
        
        $columns[] = " ( SELECT s.Caption FROM cms_Snapshot s WHERE s.ObjectId = t.DocumentId AND s.ObjectClass = '".get_class($this->getObject())."' AND s.Type = 'branch' ) DocumentVersion ";
        $columns[] = " ( SELECT s.Caption FROM WikiPage s WHERE s.WikiPageId = t.DocumentId ) DocumentName ";

        return $columns;
    }
}
