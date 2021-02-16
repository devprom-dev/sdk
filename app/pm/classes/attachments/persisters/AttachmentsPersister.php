<?php

class AttachmentsPersister extends ObjectSQLPersister
{
    function getAttributes()
    {
        return array(
            'Attachment'
        );
    }

    function getSelectColumns( $alias )
 	{
 		$columns = array();

 		$classes = array(
            strtolower($this->getObject()->getEntityRefName()),
            strtolower(get_class($this->getObject()))
        );
 		if ( in_array('request', $classes) || in_array('issue', $classes) ) {
            $classes[] = 'issue';
            $classes[] = 'increment';
        }
 		
 		$columns[] =
 		    " (SELECT GROUP_CONCAT(CAST(a.pm_AttachmentId AS CHAR)) ".
 		    "    FROM pm_Attachment a ".
 		    "   WHERE a.ObjectId = ".$this->getPK($alias).
            "     AND LCASE(a.ObjectClass) IN ('".join("','", array_unique($classes))."')".
            "  ) Attachment ";

 		return $columns;
 	}
}
