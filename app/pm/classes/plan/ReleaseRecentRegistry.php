<?php

class ReleaseRecentRegistry extends ReleaseRegistry
{
	function getSorts() {
		return array_merge (
			parent::getSorts(),
			array (
				new SortAttributeClause('StartDate.D')
			)
		);
	}
}