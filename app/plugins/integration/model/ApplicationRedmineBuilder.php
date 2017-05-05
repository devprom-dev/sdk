<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ApplicationRedmineBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
        $object->setAttributeRequired('ProjectKey', true);
        $object->setAttributeVisible('HttpUserName', false);
        $object->setAttributeVisible('HttpUserPassword', false);
        $object->setAttributeDefault('HttpHeaders', 'X-Redmine-API-Key: ');
    }
}