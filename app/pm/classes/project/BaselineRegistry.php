<?php

class BaselineRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		$snapshot = getFactory()->getObject('cms_Snapshot');
		return " (
		 	SELECT t.Stage pm_VersionId, t.CaptionType Caption, t.VPD, 0 OrderNum, t.Version, t.Release
		 	  FROM ".getFactory()->getObject('Stage')->getRegistry()->getQueryClause()." t
		 	 UNION
		 	SELECT t.Caption, t.Caption, t.VPD, 10, NULL, NULL
		 	  FROM cms_Snapshot t
		 	 WHERE t.Type = 'branch' ".$snapshot->getVpdPredicate('t')." AND t.Stage IS NULL
		) ";
	}
}