<?php

class WikiPageTraceSourceBaselinePersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();
        
        $columns[] = 
        	" ( SELECT GROUP_CONCAT(CONCAT(tr.SourcePage,':',tr.Baseline)) ".
        	"	  FROM WikiPageTrace tr ".
        	"	 WHERE tr.TargetPage = ".$this->getPK($alias)." ) SourcePageBaseline ";

        return $columns;
    }
}
