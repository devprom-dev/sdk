<?php

include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPersister.php";

class WikiPageRevisionPersister extends ObjectSQLPersister
{
	private $revision = null;
	
	function __construct( $revision )
	{
		$this->revision = $revision instanceof IteratorBase ? $revision->getId() : $revision;
		parent::__construct();
	}

	function getAttributes()
	{
		return array('Content');
	}

	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " (SELECT ch.Content FROM WikiPageChange ch ".
 					 "	 WHERE ch.WikiPage = ".$this->getPK($alias).
 				     "	   AND ch.WikiPageChangeId = ".$this->revision.") Content ";
 		
 		return $columns;
 	}
}
