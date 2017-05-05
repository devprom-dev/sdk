<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ApplicationYouTrackBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
        $object->setAttributeRequired('ProjectKey', true);
    }
}