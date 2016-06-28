<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class TestExecutionAPIModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof TestExecution ) return;

    	$object->setAttributeType('Version', "VARCHAR");
   	}
}