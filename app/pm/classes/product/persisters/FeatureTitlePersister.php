<?php

class FeatureTitlePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" IF(t.Type IS NULL, t.Caption, CONCAT_WS(': ', (SELECT l.Caption FROM pm_FeatureType l WHERE pm_FeatureTypeId = t.Type), t.Caption)) CaptionAndType "
 		);
 	}
}
