<?php
include_once SERVER_ROOT_PATH . "pm/classes/comments/Comment.php";
include "TaskCommentRegistry.php";

class TaskComment extends Comment
{
    function __construct() {
        parent::__construct(new TaskCommentRegistry($this));
        $this->setAttributeType('ObjectId', 'REF_TaskId');
        $this->setAttributeGroups('ObjectId', array());
    }

    function getDefaultAttributeValue($name) {
        return $name == 'ObjectClass' ? 'Task' : parent::getDefaultAttributeValue($name);
    }
}