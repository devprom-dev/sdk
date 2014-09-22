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
								'pm_Importance',
								'pm_ChangeRequestLinkType',
								'pm_TestExecutionResult',
							    'cms_Language'
						)
					) 
		);
	}
}