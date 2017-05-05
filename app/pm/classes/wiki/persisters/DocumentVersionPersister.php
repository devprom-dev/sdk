<?php

class DocumentVersionPersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();
        
        $columns[] = $alias.".DocumentVersion ";
        $columns[] = " ( SELECT s.Caption FROM WikiPage s WHERE s.WikiPageId = t.DocumentId ) DocumentName ";

        return $columns;
    }
}
