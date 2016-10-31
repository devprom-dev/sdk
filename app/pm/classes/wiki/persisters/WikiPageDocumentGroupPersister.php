<?php

class WikiPageDocumentGroupPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
		return array(
			" IF(WikiPageId = DocumentId, NULL, DocumentId) DocumentId "
		);
 	}
}
