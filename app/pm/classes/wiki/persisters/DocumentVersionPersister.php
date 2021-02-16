<?php

class DocumentVersionPersister extends ObjectSQLPersister
{
    function map( &$parms )
    {
        if ( $parms['DocumentVersion'] != '' ) {
            $baselineIt = getFactory()->getObject('Baseline')->getExact($parms['DocumentVersion']);
            if ( $baselineIt->getId() != '' ) {
                $parms['DocumentVersion'] = $baselineIt->getDisplayName();
            }
        }
        if ( $parms['DocumentVersion'] == '' && $parms['ParentPage'] != '' ) {
            $parentIt = $this->getObject()->getExact($parms['ParentPage']);
            $parms['DocumentVersion'] = $parentIt->get('DocumentVersion');
        }
    }

    function getSelectColumns( $alias )
    {
        $columns = array();
        
        $columns[] = $alias.".DocumentVersion ";
        $columns[] = " IF(t.ParentPage IS NULL, t.Caption, ( SELECT s.Caption FROM WikiPage s WHERE s.WikiPageId = t.DocumentId )) DocumentName ";

        return $columns;
    }
}
