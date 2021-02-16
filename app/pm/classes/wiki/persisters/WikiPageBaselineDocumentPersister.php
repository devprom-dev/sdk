<?php

class WikiPageBaselineDocumentPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('DocumentVersion', 'DocumentId');
    }

    function getSelectColumns( $alias )
    {
        return array(
            " (SELECT IFNULL(p.DocumentVersion, p.Caption) 
                 FROM WikiPage p 
                WHERE p.WikiPageId = t.ObjectId LIMIT 1) DocumentVersion ",

            " (SELECT p.DocumentId 
                 FROM WikiPage p 
                WHERE p.WikiPageId = t.ObjectId LIMIT 1) DocumentId "
        );
    }
}
