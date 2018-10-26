<?php
include_once "SetTagsWebMethod.php";

class SetTagsTaskWebMethod extends SetTagsWebMethod
{
    function getObject() {
        return getFactory()->getObject('Task');
    }
    function getTagObject() {
        return getFactory()->getObject('TaskTag');
    }
    function getAttribute() {
        return 'ObjectId';
    }
}