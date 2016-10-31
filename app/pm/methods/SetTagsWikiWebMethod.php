<?php
include_once "SetTagsWebMethod.php";

class SetTagsWikiWebMethod extends SetTagsWebMethod
{
    function getObject() {
        return getFactory()->getObject('WikiPage');
    }
    function getTagObject() {
        return getFactory()->getObject('WikiTag');
    }
    function getAttribute() {
        return 'Wiki';
    }
}