<?php

class ComponentDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" IF(t.Type IS NULL, 
 			        t.Caption, 
 			        CONCAT_WS(': ', (SELECT l.Caption 
 			                           FROM pm_ComponentType l WHERE pm_ComponentTypeId = t.Type), t.Caption)
              ) CaptionAndType "
 		);
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
