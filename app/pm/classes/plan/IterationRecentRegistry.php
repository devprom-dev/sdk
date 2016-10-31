<?php

class IterationRecentRegistry extends IterationRegistry
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