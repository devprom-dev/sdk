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
 		
 		$columns[] =
 		    " (SELECT GROUP_CONCAT(CAST(a.pm_AttachmentId AS CHAR)) ".
 		    "    FROM pm_Attachment a ".
 		    "   WHERE a.ObjectId = ".$this->getPK($alias).
            "     AND LCASE(a.ObjectClass) IN ('".strtolower($this->getObject()->getEntityRefName())."', '".strtolower(get_class($this->getObject()))."')".
            "  ) Attachment ";
 		
 		return $columns;
 	}
}
