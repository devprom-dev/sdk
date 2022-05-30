<?php

class FeatureTitlePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" IF(t.Type IS NULL, t.Caption, CONCAT_WS(': ', (SELECT l.Caption FROM pm_FeatureType l WHERE pm_FeatureTypeId = t.Type), t.Caption)) CaptionAndType ",
			" (SELECT pf.Caption FROM pm_Function pf WHERE pf.pm_FunctionId = SUBSTRING_INDEX(SUBSTR(t.ParentPath, 2), ',', 1) AND t.ParentFeature IS NOT NULL ) RootCaption ",
            " (SELECT i.Caption FROM pm_Importance i WHERE i.pm_ImportanceId = t.Importance) ImportanceName ",
            " (SELECT i.RelatedColor FROM pm_Importance i WHERE i.pm_ImportanceId = t.Importance) ImportanceColor "
 		);
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
