<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ApplicationTfsBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
        $object->setAttributeDescription('URL', text('integration32'));
        $object->setAttributeRequired('ProjectKey', true);
        $object->setAttributeRequired('HttpUserName', true);
        $object->setAttributeRequired('HttpUserPassword', false);
        $object->setAttributeCaption('HttpUserName', text('integration30'));
        $object->setAttributeDescription('HttpUserName', text('integration33'));
        $object->setAttributeCaption('HttpUserPassword', text('integration31'));
        $object->setAttributeDefault('HttpHeaders', 'X-TFS-FedAuthRedirect: Suppress');
    }
}