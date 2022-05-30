<?php

class AttachmentUnifiedRegistry extends ObjectRegistrySQL
{
    function getPersisters() {
        return array(
            new EntityProjectPersister()
        );
    }

 	function getQueryClause(array $parms)
 	{
		$sql = "
			SELECT t.pm_AttachmentId,
			       t.pm_AttachmentId SelfId,
				   t.RecordCreated,
				   t.RecordModified,
				   t.RecordVersion,
				   t.VPD,
				   t.OrderNum,
				   t.FileMime,
				   t.FilePath,
				   t.FileExt,
				   t.Description,
				   t.FileSize,
				   t.ObjectId,
				   t.ObjectClass,
				   'pm_Attachment' as AttachmentClassName
			  FROM pm_Attachment t
			 WHERE 1 = 1 ".getFactory()->getObject('Attachment')->getVpdPredicate('t');

        $type_it = getFactory()->getObject('WikiType')->getAll();
        while( !$type_it->end() )
        {
            if ( !class_exists($type_it->get('ClassName')) ) {
                $type_it->moveNext();
                continue;
            }
            $sql .= " UNION
                    SELECT 1000000 + t.WikiPageFileId,
			               t.WikiPageFileId,
                           t.RecordCreated,
                           t.RecordModified,
                           t.RecordVersion,
                           t.VPD,
                           t.OrderNum,
                           t.ContentMime,
                           t.ContentPath,
                           t.ContentExt,
                           t.Description,
                           t.FileSize,
                           t.WikiPage,
                           '".$type_it->get('ClassName')."',
                           'WikiPageFile'
                      FROM WikiPageFile t
                     WHERE 1 = 1 " . getFactory()->getObject($type_it->get('ClassName'))->getVpdPredicate('t')."
                       AND EXISTS (SELECT 1 FROM WikiPage p
                                    WHERE p.WikiPageId = t.WikiPage
                                      AND p.ReferenceName = ".$type_it->getId().")";
            $type_it->moveNext();
        }

 	    return "(".$sql.")";
 	}
}