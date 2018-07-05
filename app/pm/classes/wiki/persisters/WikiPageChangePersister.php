<?php

class WikiPageChangePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			"(SELECT COUNT(1) FROM WikiPageChange c ".
 			"  WHERE c.WikiPage = t.WikiPage AND c.WikiPageChangeId > ".$this->getPK($alias).") RecentChangesCount ";

 		return $columns;
 	}
}
