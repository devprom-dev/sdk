<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class UserAPIModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof User ) return;
        $object->setAttributeType('NotificationEmailType', 'VARCHAR');
        $object->setAttributeType('NotificationTrackingType', 'VARCHAR');
   	}
}