<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class RequestAPIModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Request ) return;
        $object->setAttributeType('StartDate', 'DATE');
        $object->setAttributeType('DeliveryDate', 'DATE');
   	}
}