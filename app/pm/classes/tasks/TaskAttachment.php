<?php
include "TaskAttachmentRegistry.php";

class TaskAttachment extends Attachment
{
    function __construct()
    {
        parent::__construct(new TaskAttachmentRegistry($this));
        $this->setAttributeType('ObjectId', 'REF_TaskId');
        $this->setAttributeGroups('ObjectId', array());
    }

    function getDefaultAttributeValue($name) {
        return $name == 'ObjectClass' ? 'task' : parent::getDefaultAttributeValue($name);
    }
}