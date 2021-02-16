<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ApplicationGitlabBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
        $object->setAttributeRequired('ProjectKey', true);
        $object->setAttributeCaption('ProjectKey', text('integration27'));
        $object->setAttributeVisible('HttpUserName', false);
        $object->setAttributeVisible('HttpUserPassword', false);
        $object->setAttributeDefault('HttpHeaders', 'Private-Token: ');
    }
}