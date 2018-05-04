<?php

class TaskSendAttachmentsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         return array (
             " (SELECT GROUP_CONCAT(CAST(a.pm_AttachmentId AS CHAR)) ".
             "    FROM pm_Attachment a, pm_CustomAttribute ca, pm_AttributeValue av ".
             "   WHERE a.ObjectId = t.ChangeRequest".
             "     AND av.ObjectId = t.pm_TaskId ".
             "     AND av.StringValue = 'Y' ".
             "     AND av.CustomAttribute = ca.pm_CustomAttributeId ".
             "     AND ca.ReferenceName = 'SendAttachments' ".
             "     AND ca.VPD = t.VPD ".
             "     AND a.ObjectClass = '".strtolower(get_class(getFactory()->getObject('Request')))."') AttachmentsToSend"
         );
     }
}