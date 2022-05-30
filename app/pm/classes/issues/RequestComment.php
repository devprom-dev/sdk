<?php
include_once SERVER_ROOT_PATH . "pm/classes/comments/Comment.php";
include "RequestCommentRegistry.php";

class RequestComment extends Comment
{
    function __construct() {
        parent::__construct(new RequestCommentRegistry($this));
        $this->setAttributeType('ObjectId', 'REF_RequestId');
        $this->setAttributeGroups('ObjectId', array());
    }

    function getDefaultAttributeValue($name) {
        return $name == 'ObjectClass' ? 'Request' : parent::getDefaultAttributeValue($name);
    }
}