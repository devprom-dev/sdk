<?php

class ProjectPageTitlePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" (SELECT IF(t.VPD = '".getSession()->getProjectIt()->get('VPD')."' OR t.ParentPage IS NOT NULL, t.Caption, (SELECT CONCAT_WS(': ', p.Caption, t.Caption) FROM pm_Project p WHERE p.VPD = t.VPD))) Caption "
 		);
 	}
}
 