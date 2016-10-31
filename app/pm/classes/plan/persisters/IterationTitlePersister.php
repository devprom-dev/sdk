<?php

class IterationTitlePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
  		$columns[] = "IFNULL((SELECT CONCAT(v.Caption, '.', t.ReleaseNumber) FROM pm_Version v WHERE v.pm_VersionId = t.Version), t.ReleaseNumber) ShortCaption ";

 		return $columns;
 	}
}
