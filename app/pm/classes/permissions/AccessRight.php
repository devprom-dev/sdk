<?php

include "AccessRightIterator.php";
include "predicates/AccessRightProjectRolePredicate.php";
include "predicates/AccessRightUserPredicate.php";
include "predicates/AccessRightTypeNonePredicate.php";
include "predicates/AccessRightTypeNoModifyPredicate.php";
include "persisters/AccessRightKeyPersister.php";

class AccessRight extends Metaobject
{
 	function AccessRight() 
 	{
 		parent::Metaobject('pm_AccessRight');

 		$this->addAttribute('RecordKey', 'VARCHAR', '', false);
 		$this->addPersister( new AccessRightKeyPersister() );
 	}
 	
	function createIterator()
	{
		return new AccessRightIterator( $this );
	}

	function getPage() 
	{
 		$info = getFactory()->getObject('Module')
 					->getExact('permissions/settings')->buildMenuItem('role='.SanitizeUrl::parseUrl($_REQUEST['role']));
 		return $info['url'];
	}
	
	function getAllForUser( $user_it )
	{
		global $model_factory;
		
		$sql = " SELECT t.*" .
			   "   FROM pm_AccessRight t, pm_ParticipantRole pr, pm_Participant p " .
			   "  WHERE t.ProjectRole = pr.ProjectRole" .
			   "    AND pr.Participant = p.pm_ParticipantId" .
			   "    AND p.SystemUser = ".$user_it->getId();
			   
		return $this->createSQLIterator($sql);
	}

	function getEntitiesForParticipant( $part_id )
	{
		global $model_factory;

		$sql = " SELECT DISTINCT t.ReferenceName " .
			   "   FROM pm_AccessRight t " .
			   "  WHERE t.VPD IN ('".join("','",$this->getVpds())."')" .
			   "    AND t.ReferenceType = 'Y'" .
			   "    AND EXISTS (SELECT 1 FROM pm_ParticipantRole p " .
			   "				 WHERE p.ProjectRole = t.ProjectRole" .
			   "				   AND p.Participant = ".$part_id." )";
			   
		return $this->createSQLIterator( $sql );
	}
}