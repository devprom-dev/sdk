<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ApplicationSlackBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
        $invisible = array('Type', 'HttpUserPassword', 'HttpHeaders');
        foreach( $invisible as $attribute ) {
            $object->setAttributeVisible($attribute, false);
        }
        $object->setAttributeCaption('ProjectKey', text('integration16'));
        $object->setAttributeDescription('ProjectKey', text('integration18'));
        $object->setAttributeRequired('ProjectKey', true);

        $object->setAttributeCaption('URL', text('integration17'));
        $object->setAttributeDescription('URL', text('integration19'));
        $object->setAttributeDefault('URL', '#general');

        $object->setAttributeCaption('HttpUserName', text('integration21'));
        $object->setAttributeDescription('HttpUserName', text('integration22'));
        $object->setAttributeDefault('HttpUserName', 'devprom');

        $object->setAttributeDefault('Type', 'readwrite');
    }
}