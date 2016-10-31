<?php

class TaskFactPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array (
 			"(SELECT ROUND(SUM(ac.Capacity),1) FROM pm_Activity ac WHERE ac.Task = ".$this->getPK($alias)." ) Fact "
		);
 	}
}
