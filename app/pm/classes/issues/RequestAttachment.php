<?php
include "RequestAttachmentRegistry.php";

class RequestAttachment extends Attachment
{
    function __construct()
    {
        parent::__construct(new RequestAttachmentRegistry($this));
        $this->setAttributeType('ObjectId', 'REF_RequestId');
        $this->setAttributeGroups('ObjectId', array());
    }

    function getDefaultAttributeValue($name) {
        return $name == 'ObjectClass' ? 'request' : parent::getDefaultAttributeValue($name);
    }
}