<?php
include "persisters/ChangeLogWhatsNewPersister.php";

class ChangeLogWhatsNewRegistry extends ChangeLogRegistry
{
	public function getPersisters() {
		return array (
            new ChangeLogWhatsNewPersister(),
            new EntityProjectPersister()
        );
	}

	public function getGroups()
	{
		return array (
            new GroupAttributeClause('Caption'),
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