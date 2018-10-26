<?php

class FeatureTagPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    return array(
            " (SELECT GROUP_CONCAT(CAST(wt.Tag AS CHAR)) " .
            " 	 FROM pm_CustomTag wt " .
            "  	WHERE wt.ObjectId = ".$this->getPK($alias).
            "	  AND wt.ObjectClass = '".getFactory()->getObject('FeatureTag')->getObjectClass()."' ) Tags ",

            " (SELECT GROUP_CONCAT(tg.Caption) " .
            " 	 FROM pm_CustomTag wt, Tag tg " .
            "  	WHERE wt.ObjectId = ".$this->getPK($alias).
            "     AND wt.Tag = tg.TagId ".
            "	  AND wt.ObjectClass = '".getFactory()->getObject('FeatureTag')->getObjectClass()."' ) TagNames "
        );
 	}

    function IsPersisterImportant() {
        return true;
    }
} 