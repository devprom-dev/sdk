<?php

class ReleaseActualRegistry extends ReleaseRegistry
{
	function getFilters() {
		return array_merge (
			parent::getFilters(),
			array (
				new ReleaseTimelinePredicate('not-passed')
			)
		);
	}
}