<?php

class DocumentVersionPersister extends ObjectSQLPersister
{
    function map( &$parms )
    {
        if ( $parms['DocumentVersion'] == '' && $parms['ParentPage'] != '' ) {
            $parentIt = $this->getObject()->getExact($parms['ParentPage']);
            $parms['DocumentVersion'] = $parentIt->get('DocumentVersion');
        }
    }

    function getSelectColumns( $alias )
    {
        $columns = array();
        
        $columns[] = $alias.".DocumentVersion ";
        $columns[] = " ( SELECT s.Caption FROM WikiPage s WHERE s.WikiPageId = t.DocumentId ) DocumentName ";

        return $columns;
    }
}
