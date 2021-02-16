<?php
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPersister.php";

class ObjectQuarterDatesPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array ('QuarterCreated', 'QuarterModified', 'QuarterFinished');
	}

	function getSelectColumns( $alias )
 	{
		$columns = array (
			" YEAR(t.RecordCreated) * 10 + QUARTER(t.RecordCreated) QuarterCreated ",
			" YEAR(t.RecordModified) * 10 + QUARTER(t.RecordModified) QuarterModified "
		);

		if ( $this->getObject()->getAttributeType('FinishDate') != '' ) {
            $columns[] = " YEAR(t.FinishDate) * 10 + QUARTER(t.FinishDate) QuarterFinished ";
        }

		return $columns;
 	}
}
