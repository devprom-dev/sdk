<?php

class IterationTitlePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
            "IFNULL((SELECT CONCAT(v.Caption, '.', t.Caption) 
                       FROM pm_Version v WHERE v.pm_VersionId = t.Version), t.Caption) FullCaption "
        );
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
