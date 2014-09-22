<?php

class WikiPageDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();

 		$objectPK = $this->getPK($alias);
 		
 		$columns[] = " (SELECT COUNT(1) FROM WikiPage t2 WHERE t2.ParentPage = ".$objectPK.") TotalCount ";
 		
 		$columns[] = " ( SELECT GROUP_CONCAT(CAST(tr.SourcePage AS CHAR)) FROM WikiPageTrace tr WHERE tr.TargetPage = ".$objectPK." AND tr.IsActual = 'N' AND IFNULL(tr.Baseline, 0) < 1) BrokenTraces ";
 		
 		return $columns;
 	}
}
