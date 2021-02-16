<?php

class SetupCalendar extends Installable 
{
    function check()
    {
        return true;
    }

    function install()
    {
        getFactory()->getObject('Calendar')->getAll();

        $attachmentIt = getFactory()->getObject('pm_Attachment')->getRegistry()->Query(
            array(
                new FilterAttributeNullPredicate('FileSize')
            )
        );
        while( !$attachmentIt->end() ) {
            $fileSize = max(0, filesize($attachmentIt->getFilePath('File')));
            DAL::Instance()->Query("UPDATE pm_Attachment SET FileSize = ".$fileSize." WHERE pm_AttachmentId = " . $attachmentIt->getId());
            $attachmentIt->moveNext();
        }

        return true;
    }
}
