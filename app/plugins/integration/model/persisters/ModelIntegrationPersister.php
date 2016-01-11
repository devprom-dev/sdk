<?php

 class ModelIntegrationPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		return array(
	 		" (SELECT l.URL FROM pm_IntegrationLink l
	 		    WHERE l.ObjectClass = '".get_class($this->getObject())."'
	 		      AND l.ObjectId = ".$this->getPK($alias).") IntegrationLink "

	 	);
 	}
 }
