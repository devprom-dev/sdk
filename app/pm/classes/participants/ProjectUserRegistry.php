<?php

class ProjectUserRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new UserWorkerPredicate()
				)
		);
	}

	function getPersisters()
	{
		return array_merge(
			array (
				new UserParticipatesDetailsPersister()
			),
			parent::getPersisters()
		);
	}
}