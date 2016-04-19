<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ActivityMetadataBuilderExTiming extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Activity ) return; // extend the model for Activity entity only

        $metadata->addAttribute('TaskType', 'REF_TaskTypeId', translate('Тип задачи'), true, false, '', 15);
        $metadata->setAttributeRequired('TaskType', true);

        $tasktype = new Metaobject('pm_TaskType');
        $type_it = $tasktype->getRegistry()->Query(
            array(
                new FilterVpdPredicate(),
                new FilterAttributePredicate('IsDefault','Y')
            )
        );
        if ( $type_it->getId() > 0 ) {
            $attributes = $metadata->getAttributes();
            $attributes['TaskType']['default'] = $type_it->getId();
            $metadata->setAttributes($attributes);
        }
    }
}
