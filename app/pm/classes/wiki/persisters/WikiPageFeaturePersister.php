<?php

class WikiPageFeaturePersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('Feature');
	}

	function getSelectColumns( $alias )
 	{
 		return array (
			" IFNULL( ".
			" 	(SELECT GROUP_CONCAT(DISTINCT CAST(r.Feature AS CHAR)) ".
			"      FROM pm_FunctionTrace r " .
			"  	  WHERE r.ObjectId = ".$this->getPK($alias).
			"       AND r.ObjectClass IN ('Requirement','TestScenario','HelpPage') ), ".
			" 	IFNULL( ".
			" 		(SELECT GROUP_CONCAT(DISTINCT CAST(r.Feature AS CHAR)) ".
			"      	   FROM pm_FunctionTrace r, WikiPageTrace tr " .
			"  	  	  WHERE r.ObjectId = tr.SourcePage ".
			"    		AND tr.TargetPage = ".$this->getPK($alias)." ), ".
 			"		(SELECT GROUP_CONCAT(DISTINCT CAST(r.Function AS CHAR)) ".
 			"      	   FROM pm_ChangeRequest r, pm_ChangeRequestTrace tr " .
			"  	  	  WHERE r.pm_ChangeRequestId = tr.ChangeRequest ".
 		    "    		AND tr.ObjectId = ".$this->getPK($alias)." ".
 		    "    		AND tr.ObjectClass = '".strtolower(get_class($this->getObject()))."')".
			"	)".
			" ) Feature "
		);
 	}
}
