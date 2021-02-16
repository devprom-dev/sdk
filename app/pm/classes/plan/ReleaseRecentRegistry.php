<?php

class ReleaseRecentRegistry extends ReleaseRegistry
{
    function getFilters() {
        return array_merge (
            parent::getFilters(),
            array (
                new FilterAttributePredicate('IsClosed', 'N')
            )
        );
    }

	function getSorts() {
		return array_merge (
			parent::getSorts(),
			array (
				new SortAttributeClause('StartDate.D')
			)
		);
	}
}