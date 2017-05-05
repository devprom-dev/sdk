<?php

class SystemDictionaryRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array(
				new FilterAttributePredicate('ReferenceName', 
						array (
                            'pm_ProjectRole',
                            'pm_TaskType',
                            'Priority',
                            'pm_Severity',
                            'pm_Importance',
                            'pm_ChangeRequestLinkType',
                            'cms_Language'
						)
					) 
		);
	}
}