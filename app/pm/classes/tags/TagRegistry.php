<?php

class TagRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
	    return " (
            SELECT t.TagId, t.RecordCreated, t.RecordModified, t.VPD, t.OrderNum, t.Caption, t.RecordVersion
              FROM Tag t
             UNION
            SELECT t.TagId, t.RecordCreated, t.RecordModified, rt.VPD, t.OrderNum, t.Caption, t.RecordVersion
              FROM Tag t, pm_RequestTag rt
             WHERE t.TagId = rt.Tag
             UNION
            SELECT t.TagId, t.RecordCreated, t.RecordModified, wt.VPD, t.OrderNum, t.Caption, t.RecordVersion
              FROM Tag t, WikiTag wt
             WHERE t.TagId = wt.Tag
        ) ";
	}
}