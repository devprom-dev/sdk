<?php
include "persisters/ChangeLogAggregatePersister.php";

class ChangeLogAggregatedRegistry extends ChangeLogRegistry
{
	public function getPersisters() {
		return array_merge(
			array (
				new ChangeLogAggregatePersister()
			),
			parent::getPersisters()
		);
	}

	public function getGroups()
	{
		return array (
			new GroupAttributeClause('FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP(RecordModified) / 86400)*86400)'),
			new GroupAttributeClause('Author'),
			new GroupAttributeClause('SystemUser'),
			new GroupAttributeClause('Caption'),
			new GroupAttributeClause('ObjectId'),
			new GroupAttributeClause('ClassName'),
			new GroupAttributeClause('EntityRefName'),
			new GroupAttributeClause('VPD'),
			new GroupAttributeClause('ChangeKind'),
			new GroupAttributeClause('Transaction')
		);
	}

	public function getSelectClause( $alias, $select_all = true ) {
		return parent::getSelectClause($alias, false);
	}

	public function getLimit() {
		return 256;
	}
}