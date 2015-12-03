<?php

class WikiTagsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();

 		$columns[] = "(SELECT GROUP_CONCAT(wt.Tag) FROM WikiTag wt WHERE wt.Wiki = ".$this->getPK($alias)." ) Tags ";

 		return $columns;
 	}
}
