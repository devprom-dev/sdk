<?php

class WikiPageBrokenTracesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$objectPK = $this->getPK($alias);

		$columns = array(
            " (SELECT GROUP_CONCAT(CAST(tr.WikiPageTraceId AS CHAR)) 
                 FROM WikiPageTrace tr 
                WHERE tr.TargetPage = ".$objectPK." AND tr.IsActual = 'N' AND IFNULL(tr.Baseline, 0) < 1) BrokenTraces ",

            " (SELECT GROUP_CONCAT(CAST(tr.pm_FunctionTraceId AS CHAR)) 
                 FROM pm_FunctionTrace tr 
                WHERE tr.ObjectId = ".$objectPK." AND tr.IsActual = 'N') BrokenFeatures ",
		);

 		return $columns;
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
