<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/StateDurationPersister.php";

class WorkflowModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof MetaobjectStatable ) return;
 	    if ( $object->getStateClassName() == '' ) return;

		$object->addPersister( new StateDurationPersister(array('LeadTime','StateDuration')) );
    }
}