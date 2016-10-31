<?php
include_once "SetTagsWebMethod.php";

class SetTagsRequestWebMethod extends SetTagsWebMethod
{
    function getObject() {
        return getFactory()->getObject('Request');
    }
    function getTagObject() {
        return getFactory()->getObject('RequestTag');
    }
    function getAttribute() {
        return 'Request';
    }
}