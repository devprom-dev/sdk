<?php

class BaselineRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		$snapshot = getFactory()->getObject('cms_Snapshot');
		return " (
		 	SELECT t.CaptionType pm_VersionId, t.CaptionType Caption, t.VPD, 0 OrderNum
		 	  FROM ".getFactory()->getObject('Stage')->getRegistry()->getQueryClause()." t
		 	 UNION
		 	SELECT t.Caption, t.Caption, t.VPD, 0
		 	  FROM cms_Snapshot t
		 	 WHERE t.Type = 'branch' ".$snapshot->getVpdPredicate('t')."
		) ";
	}
}