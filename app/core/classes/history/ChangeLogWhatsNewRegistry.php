<?php
include "persisters/ChangeLogWhatsNewPersister.php";

class ChangeLogWhatsNewRegistry extends ChangeLogRegistry
{
	public function getPersisters() {
		return array (
            new ChangeLogWhatsNewPersister()
        );
	}

	public function getGroups()
	{
		return array_merge(
		    parent::getGroups(),
		    array (
                new GroupAttributeClause('Author'),
                new GroupAttributeClause('SystemUser'),
                new GroupAttributeClause('ObjectId'),
                new GroupAttributeClause('ClassName'),
                new GroupAttributeClause('VPD')
            )
        );
	}

    public function getSelectClause( $persisters = array(), $alias = 't', $select_all = true ) {
		return parent::getSelectClause($persisters, $alias, false);
	}

	public function getLimit() {
		return 1024;
	}
}