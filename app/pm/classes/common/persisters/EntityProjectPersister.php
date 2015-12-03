<?php

class EntityProjectPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
			"(SELECT r.pm_ProjectId FROM pm_Project r WHERE r.VPD = t.VPD LIMIT 1) Project ",
			"(SELECT r.CodeName FROM pm_Project r WHERE r.VPD = t.VPD LIMIT 1) ProjectCodeName "
		);
 	}
}
