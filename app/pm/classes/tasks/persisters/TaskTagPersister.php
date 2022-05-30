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

    function modify( $object_id, $parms )
    {
        if ( trim($parms['Tags']) == '' ) return;

        $tag = getFactory()->getObject('TaskTag');
        $tag->removeTags( $object_id );

        foreach( preg_split('/,/', $parms['Tags']) as $tag_id ) {
            $tag->bindToObject( $object_id, $tag_id );
        }
    }

    function add($object_id, $parms)
    {
        if ( trim($parms['Tags']) == '' ) return;

        $tag = getFactory()->getObject('TaskTag');
        foreach( preg_split('/,/', $parms['Tags']) as $tag_id ) {
            $tag->bindToObject( $object_id, $tag_id );
        }
    }

    function IsPersisterImportant() {
        return true;
    }
} 