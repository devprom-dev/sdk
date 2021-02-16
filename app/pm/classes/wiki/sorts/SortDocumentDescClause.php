<?php

class SortDocumentDescClause extends SortClauseBase
{
 	function clause()
 	{
 		return " DocumentId, SortIndex DESC ";
 	}
}
