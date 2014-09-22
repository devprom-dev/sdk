<?php

include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPersister.php";

class WikiPageRevisionPersister extends ObjectSQLPersister
{
	private $revision_it = null;
	
	function __construct( $revision_it )
	{
		$this->revision_it = $revision_it; 
		
		parent::__construct();
	}
	
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " (SELECT ch.Content FROM WikiPageChange ch ".
 					 "	 WHERE ch.WikiPage = ".$this->getPK($alias).
 				     "	   AND ch.WikiPageChangeId = ".$this->revision_it->getId().") Content ";
 		
 		return $columns;
 	}
}
