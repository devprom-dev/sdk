<?php

class ChangeLogAggregatedRegistry extends ChangeLogRegistry
{
	public function getGroups()
	{
		return array (
				new GroupAttributeClause('FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP(RecordModified) / 180)*180)'),
				new GroupAttributeClause('Author'),
				new GroupAttributeClause('SystemUser'),
				new GroupAttributeClause('Caption'),
				new GroupAttributeClause('ObjectId'),
				new GroupAttributeClause('ClassName'),
				new GroupAttributeClause('EntityRefName'),
				new GroupAttributeClause('VPD')
		);
	}
}