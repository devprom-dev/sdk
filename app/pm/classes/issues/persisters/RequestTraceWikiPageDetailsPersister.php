<?php

class RequestTraceWikiPageDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " ( SELECT doc.Caption FROM WikiPage sp, WikiPage doc WHERE doc.WikiPageId = sp.DocumentId AND sp.WikiPageId = t.ObjectId ) SourceDocumentName ";
        $columns[] = " ( SELECT doc.DocumentVersion FROM WikiPage sp, WikiPage doc WHERE doc.WikiPageId = sp.DocumentId AND sp.WikiPageId = t.ObjectId ) SourceDocumentVersion ";

 		return $columns;
 	}
}
