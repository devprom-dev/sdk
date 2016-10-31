<?php

class WikiPageDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$objectPK = $this->getPK($alias);

		$columns = array(
		    " CONCAT((SELECT IFNULL(CONCAT(MAX(t2.Caption), ' / '),'') FROM WikiPage t2 WHERE ".$alias.".ParentPage = t2.WikiPageId AND t2.ParentPage IS NOT NULL), ".$alias.".Caption) CaptionLong ",
            " IFNULL(( SELECT 1 FROM WikiPage t2 WHERE t2.ParentPage = ".$objectPK." LIMIT 1), 0) TotalCount ",
			" ( SELECT GROUP_CONCAT(CAST(tr.SourcePage AS CHAR)) FROM WikiPageTrace tr WHERE tr.TargetPage = ".$objectPK." AND tr.IsActual = 'N' AND IFNULL(tr.Baseline, 0) < 1) BrokenTraces "
		);

 		return $columns;
 	}

	function map( & $parms )
	{
		if ( $parms['Content'] != '' ) {
			$parms['Content'] = TextUtils::getValidHtml($parms['Content']);
		}
	}
}
