<?php
include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';

class DocNewChildWebMethod extends ObjectCreateNewWebMethod
{
    function __construct($object = null)
    {
        parent::__construct($object);
        $this->setRedirectUrl('function(jsonText){onPageAdded(jsonText);}');
    }
}