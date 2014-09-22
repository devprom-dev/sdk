<?php

class WikiPageTraceKeyPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " CONCAT_WS(',',t.SourcePage,t.TargetPage) RecordKey ";

		$columns[] = " ( SELECT sp.DocumentId FROM WikiPage sp WHERE sp.WikiPageId = t.SourcePage ) SourceDocumentId ";

		$columns[] = " ( SELECT sp.DocumentId FROM WikiPage sp WHERE sp.WikiPageId = t.TargetPage ) TargetDocumentId ";
		
		$columns[] = " ( SELECT doc.Caption FROM WikiPage sp, WikiPage doc WHERE doc.WikiPageId = sp.DocumentId AND sp.WikiPageId = t.SourcePage ) SourceDocumentName ";
 		
		$columns[] = " ( SELECT doc.Caption FROM WikiPage sp, WikiPage doc WHERE doc.WikiPageId = sp.DocumentId AND sp.WikiPageId = t.TargetPage ) TargetDocumentName ";
		
		$columns[] = " ( SELECT tp.ReferenceName FROM WikiPage tp WHERE tp.WikiPageId = t.TargetPage ) TargetPageReferenceName ";
 		
 		$columns[] = " ( SELECT sp.ReferenceName FROM WikiPage sp WHERE sp.WikiPageId = t.SourcePage ) SourcePageReferenceName ";
 		
 		return $columns;
 	}
}
