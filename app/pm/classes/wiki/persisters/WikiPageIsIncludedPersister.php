<?php

class WikiPageIsIncludedPersister extends ObjectSQLPersister
{
	function getAttributes()
	{
		return array('IncludedIn');
	}

	function getSelectColumns( $alias )
 	{
 		return array(
			" (SELECT GROUP_CONCAT(CAST(ub.ParentPage AS CHAR)) FROM WikiPage ub WHERE ub.Includes = t.WikiPageId) IncludedIn "
		);
 	}
}
