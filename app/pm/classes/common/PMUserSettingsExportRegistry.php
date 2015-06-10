<?php

class PMUserSettingsExportRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
					new SettingExportPredicate(),
					new FilterBaseVpdPredicate()
				)
		);
	}
	
	function getSorts()
	{
		return array_merge(
				parent::getSorts(),
				array (
						new SortAttributeClause('Participant.D')
				)
		);
	}
}
