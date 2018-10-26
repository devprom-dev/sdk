<?php

class TaskTagPersister extends ObjectSQLPersister
{
    function getAttributes()
    {
        return array(
            'Tags'
        );
    }

    function getSelectColumns( $alias )
 	{
 	    return array(
            " (SELECT GROUP_CONCAT(CAST(wt.Tag AS CHAR)) " .
            " 	 FROM pm_CustomTag wt " .
            "  	WHERE wt.ObjectId = ".$this->getPK($alias).
            "	  AND wt.ObjectClass = '".getFactory()->getObject('TaskTag')->getObjectClass()."' ) Tags ",

            " (SELECT GROUP_CONCAT(g.Caption) " .
            " 	 FROM pm_CustomTag wt, Tag g " .
            "  	WHERE g.TagId = wt.Tag ".
            "     AND wt.ObjectId = ".$this->getPK($alias).
            "	  AND wt.ObjectClass = '".getFactory()->getObject('TaskTag')->getObjectClass()."' ) TagNames "
        );
 	}

    function IsPersisterImportant() {
        return true;
    }
} 