<?php

class IterationRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		$columns = array(
				"(SELECT CONCAT(v.Caption, '.', IFNULL((SELECT CONCAT_WS('', t.ReleaseNumber,' [',st.Caption,']') FROM pm_ProjectStage st WHERE st.pm_ProjectStageId = t.ProjectStage), t.ReleaseNumber)) FROM pm_Version v WHERE v.pm_VersionId = t.Version) Caption"
		);
		foreach( $this->getObject()->getAttributes() as $attribute => $data ) {
			if ( !$this->getObject()->IsAttributeStored($attribute) ) continue;
			$columns[] = $attribute;
		}
	    return " (SELECT ".join(',',$columns).", ".
		       "		 t.VPD,".
		       "		 t.pm_ReleaseId,".
	    	   "	     DATE(t.StartDate) StartDateOnly, ".
	    	   "		 DATE(t.FinishDate) FinishDateOnly, ".
	    	   "		 DATE(GREATEST(NOW(), t.StartDate)) AdjustedStart, ".
	    	   "		 DATE(LEAST(GREATEST(NOW(), t.StartDate), t.FinishDate)) AdjustedFinish ".
	    	   "	FROM pm_Release t ) ";
	}
}