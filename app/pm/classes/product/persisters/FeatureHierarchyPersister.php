<?php

class FeatureHierarchyPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('ChildrenLevels');
    }

    function IsPersisterImportant() {
        return true;
    }

    function getSelectColumns( $alias )
 	{
 		return array( 
 			" (SELECT tp.ChildrenLevels FROM pm_FeatureType tp WHERE tp.pm_FeatureTypeId = t.Type) ChildrenLevels ",
 			" t.Type FeatureLevel "
 		);
 	}
}
