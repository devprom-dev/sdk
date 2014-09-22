<?php

class TaskTraceWikiPageDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " ( SELECT doc.Caption FROM WikiPage sp, WikiPage doc WHERE doc.WikiPageId = sp.DocumentId AND sp.WikiPageId = t.ObjectId ) SourceDocumentName ";
 		
 		return $columns;
 	}
}
