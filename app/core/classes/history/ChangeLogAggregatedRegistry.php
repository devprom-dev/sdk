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
			new GroupAttributeClause('SystemUser'),
            new GroupAttributeClause('ChangeKind'),
            new GroupAttributeClause('Author'),
			new GroupAttributeClause('ObjectId'),
			new GroupAttributeClause('ClassName'),
			new GroupAttributeClause('VPD')
		);
	}

	public function getSelectClause( $alias, $select_all = true ) {
		return parent::getSelectClause($alias, false);
	}

    public function Count( $parms = array() )
    {
        $this->setParameters( $parms );

        $sql = 'SELECT '.$this->getSelectClause('t').' FROM '.$this->getQueryClause().' t WHERE 1 = 1 '.$this->getFilterPredicate();

        $group = $this->getGroupClause('t');
        if ( $group != '' ) $sql .= ' GROUP BY '.$group;

        return $this->createSQLIterator(
                'SELECT COUNT(1) cnt FROM ('.$sql.') t '
            )->get('cnt');
    }

	public function getLimit() {
		return 256;
	}
}