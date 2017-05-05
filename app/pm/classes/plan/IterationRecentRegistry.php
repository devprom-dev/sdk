<?php

class IterationRecentRegistry extends IterationRegistry
{
	function getSorts() {
		return array_merge (
            array (
                new SortAttributeClause('StartDate.D')
            ),
			parent::getSorts()
		);
	}
}