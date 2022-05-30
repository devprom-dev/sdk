<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/RequestTaskTypePlannedPersister.php";

class RequestTaskTypeModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
        if ( ! $object instanceof Request ) return;

        $taskTypeIt = getFactory()->getObject('TaskTypeUnified')->getAll();
        $attributeOrderNumber = 82;
        while( !$taskTypeIt->end() ) {
            if ( $taskTypeIt->getId() == 'z' ) {
                $taskTypeIt->moveNext();
                continue;
            }
            $attribute = 'Planned'.$taskTypeIt->getId();
            $object->addAttribute($attribute, 'FLOAT',
                $taskTypeIt->getDisplayName(), true, false, '', $attributeOrderNumber++);
            $object->setAttributeEditable($attribute, false);
            $object->addAttributeGroup($attribute, 'hours');
            $object->addAttributeGroup($attribute, 'workload');
            $taskTypeIt->moveNext();
        }

        $object->addPersister( new RequestTaskTypePlannedPersister() );
    }
}