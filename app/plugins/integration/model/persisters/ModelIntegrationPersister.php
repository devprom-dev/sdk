<?php

 class ModelIntegrationPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		return array(
	 		" (SELECT MAX(l.URL) FROM pm_IntegrationLink l
	 		    WHERE l.ObjectClass = '".get_class($this->getObject())."'
	 		      AND l.ObjectId = ".$this->getPK($alias).") IntegrationLink ",
	 		" (SELECT GROUP_CONCAT(CAST(l.pm_IntegrationLinkId AS CHAR)) FROM pm_IntegrationLink l
	 		    WHERE l.ObjectClass = '".get_class($this->getObject())."'
	 		      AND l.ObjectId = ".$this->getPK($alias).") IntegrationRef "
	 	);
 	}
 }
