<?php

class IterationTitlePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
  		$columns[] = "(SELECT CONCAT(v.Caption, '.', IFNULL((SELECT CONCAT_WS('', t.ReleaseNumber,' [',st.Caption,']') FROM pm_ProjectStage st WHERE st.pm_ProjectStageId = t.ProjectStage), t.ReleaseNumber)) FROM pm_Version v WHERE v.pm_VersionId = t.Version) Caption ";
 		
 		return $columns;
 	}
}
