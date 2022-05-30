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
			new GroupAttributeClause('FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP(RecordModified) / 600) * 600)'),
			new GroupAttributeClause('SystemUser'),
            new GroupAttributeClause('ChangeKind'),
            new GroupAttributeClause('Author'),
			new GroupAttributeClause('ObjectId'),
			new GroupAttributeClause('ClassName'),
			new GroupAttributeClause('VPD')
		);
	}

	public function getSelectClause( $persisters = array(), $alias = 't', $select_all = true ) {
		return parent::getSelectClause($persisters, $alias, false);
	}

	public function getLimit() {
		return 256;
	}
}