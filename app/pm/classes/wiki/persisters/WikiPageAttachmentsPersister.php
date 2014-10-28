<?php

class WikiPageAttachmentsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =
 		    " (SELECT GROUP_CONCAT(CAST(a.WikiPageFileId AS CHAR)) ".
 		    "    FROM WikiPageFile a ".
 		    "   WHERE a.WikiPage = ".$this->getPK($alias).
            "  ) Attachments ";
 		
 		return $columns;
 	}
}
