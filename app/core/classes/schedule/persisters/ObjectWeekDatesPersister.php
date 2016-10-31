<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPersister.php";

class ObjectWeekDatesPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array ('WeekCreated', 'WeekModified');
	}

	function getSelectColumns( $alias )
 	{
		return array (
			" YEARWEEK(t.RecordCreated) WeekCreated ",
			" YEARWEEK(t.RecordModified) WeekModified "
		);
 	}
}
