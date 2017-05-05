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
	
	function getQueryClause()
    {
        return " 
            (SELECT * 
               FROM ".parent::getQueryClause()."
              WHERE (Participant, Setting, VPD) IN (
                        SELECT MAX(t2.Participant) Participant, t2.Setting, t2.VPD
                          FROM ".parent::getQueryClause()." t2
                          GROUP BY t2.Setting, t2.VPD
                    )
             ) ";
    }
}
