<?php

class SystemDictionaryRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
	    $entities = array_filter(
	        array (
                'pm_ProjectRole',
                'pm_TaskType',
                'Priority',
                'pm_Severity',
                'pm_Importance',
                'pm_ChangeRequestLinkType',
                'cms_Language'
            ),
            function($class) {
	            return class_exists(getFactory()->getClass($class));
            }
        );
		return array(
            new FilterAttributePredicate('ReferenceName', $entities )
		);
	}
}