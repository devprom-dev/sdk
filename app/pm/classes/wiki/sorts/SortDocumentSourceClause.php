<?php

class SortDocumentSourceClause extends SortClauseBase
{
 	function clause()
 	{
 		return " IFNULL((SELECT MAX(tr.SourcePage) FROM WikiPageTrace tr WHERE tr.TargetPage = t.WikiPageId) * 1000000 + t.WikiPageId, t.WikiPageId) ASC ";
 	}
}
