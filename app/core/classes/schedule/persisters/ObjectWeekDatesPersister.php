<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPersister.php";

class ObjectWeekDatesPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array ('WeekCreated', 'WeekModified', 'WeekFinished');
	}

	function getSelectColumns( $alias )
 	{
		$columns = array (
			" YEARWEEK(t.RecordCreated) WeekCreated ",
			" YEARWEEK(t.RecordModified) WeekModified "
		);

		if ( $this->getObject()->getAttributeType('FinishDate') != '' ) {
            $columns[] = " YEARWEEK(t.FinishDate) WeekFinished ";
        }

		return $columns;
 	}
}
