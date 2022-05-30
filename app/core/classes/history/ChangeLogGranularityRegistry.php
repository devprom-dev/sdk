<?php

include "persisters/ChangeLogGranularityPersister.php";

class ChangeLogGranularityRegistry extends ChangeLogRegistry
{
	const DAY = 86400;
	const HOUR = 3600;
	const SECOND = 1;
	 
	public function getGroups()
	{
		return array (
				new GroupAttributeClause('FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP(RecordModified) / '.$this->granularity.') * '.$this->granularity.')'),
				new GroupAttributeClause('ChangeKind'),
				new GroupAttributeClause('ObjectId'),
				new GroupAttributeClause('ClassName'),
				new GroupAttributeClause('EntityRefName'),
				new GroupAttributeClause('VPD')
		);
	}
	
	public function getPersisters()
	{
		return array_merge(
			array ( new ChangeLogGranularityPersister() )
		);
	}

	public function getSorts()
	{
		return array_merge(
			array ( new SortChangeLogRecentClause() )
		);
	}
	
	public function getQueryClause(array $parms)
	{
		return " (SELECT t.*, 
		                 (SELECT GROUP_CONCAT(DISTINCT a.Attributes ORDER BY a.Attributes) 
		                    FROM ObjectChangeLogAttribute a WHERE a.ObjectChangeLogId = t.ObjectChangeLogId) Attributes 
			     	FROM ObjectChangeLog t WHERE 1 = 1 {$this->getFilterPredicate($this->extractPredicates($parms))}) ";
	}
	
	public function setGranularity( $granularity )
	{
		$this->granularity = $granularity;
	}

	public function getSelectClause( $persisters = array(), $alias = 't', $select_all = true ) {
		return parent::getSelectClause( $persisters, $alias, false);
	}

	private $granularity = 1;
}