<?php
include "TaskCommentRegistry.php";

class TaskComment extends Comment
{
    function __construct() {
        parent::__construct(new TaskCommentRegistry($this));
        $this->setAttributeType('ObjectId', 'REF_TaskId');
        $this->setAttributeGroups('ObjectId', array());
        $this->setAttributeRequired('AuthorId', false);
    }

    function getDefaultAttributeValue($name) {
        return $name == 'ObjectClass' ? 'Task' : parent::getDefaultAttributeValue($name);
    }
}