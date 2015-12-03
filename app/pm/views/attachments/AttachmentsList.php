<?php

class AttachmentsList extends PMPageList
{
    function drawCell( $object_it, $attr )
    {
        switch( $attr )
        {
            case 'File':
                echo $object_it->getFileLink();
                break;

            case 'Size':
                echo $object_it->getFileSizeKb('Content');
                echo ' KB';
                break;

            case 'ObjectId':
                $this->getUidService()->drawUidInCaption($object_it->getAnchorIt());
                break;

            default:
                parent::drawCell( $object_it, $attr );
        }
    }

    function getGroupDefault() {
        return '';
    }
}