<?php

class SortDocumentClause extends SortClauseBase
{
 	function clause()
 	{
 		return " DocumentId, SortIndex ";
 	}
}
