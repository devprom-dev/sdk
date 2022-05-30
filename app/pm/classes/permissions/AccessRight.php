<?php
include "AccessRightIterator.php";
include "predicates/AccessRightUserPredicate.php";
include "persisters/AccessRightKeyPersister.php";

class AccessRight extends Metaobject
{
 	function __construct()
 	{
 		parent::__construct('pm_AccessRight');
 		$this->addAttribute('RecordKey', 'VARCHAR', '', false);
 		$this->addPersister( new AccessRightKeyPersister() );
        $this->setAttributeEditable('ProjectRole', false);
 	}
 	
	function createIterator() {
		return new AccessRightIterator( $this );
	}

	function getPage() 
	{
 		$info = getFactory()->getObject('Module')
 					->getExact('permissions/settings')->buildMenuItem('role='.SanitizeUrl::parseUrl($_REQUEST['role']));
 		return $info['url'];
	}
}