<?php

class IterationActualRegistry extends IterationRegistry
{
	function getFilters() {
		return array_merge (
			parent::getFilters(),
			array (
				new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED)
			)
		);
	}
}