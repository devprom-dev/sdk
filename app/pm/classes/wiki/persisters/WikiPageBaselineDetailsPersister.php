<?php

class WikiPageBaselineDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$objectPK = $this->getPK($alias);
 		return array(
            " (SELECT GROUP_CONCAT(CAST(tr.SourcePage AS CHAR)) 
                 FROM WikiPageTrace tr WHERE tr.TargetPage = ".$objectPK." AND tr.Type = 'branch') SourceBaselinePages ",
        );
 	}
}
