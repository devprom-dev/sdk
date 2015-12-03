<?php

class FeatureRequestPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.pm_ChangeRequestId AS CHAR)) " .
			"     FROM pm_ChangeRequest l " .
			"    WHERE l.Function = " .$this->getPK($alias)." ) Request ",
			" ( SELECT SUM(l.Fact) " .
			"     FROM pm_ChangeRequest l " .
			"    WHERE l.Function = " .$this->getPK($alias)." ) Fact "
 		);
 	}
}
