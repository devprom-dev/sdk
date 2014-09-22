<?php

class WorkTableProjectRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		$program_it = WorkTableProject::getProgramIt();
		
		return array (
				new FilterInPredicate(
						array_merge( array($program_it->getId()), preg_split('/,/', $program_it->get('LinkedProject')) )
				)
		);
	}
}