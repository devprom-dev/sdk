<?php

class AttributePermissionEntityRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        global $model_factory;
        
        return $this->createIterator( array (
                array (
                        'entityId' => 'pm_ChangeRequest',
                        'ReferenceName' => 'pm_ChangeRequest',
                        'Caption' => $model_factory->getObject('pm_ChangeRequest')->getDisplayName()
                ),
                array (
                        'entityId' => 'pm_Task',
                        'ReferenceName' => 'pm_Task',
                        'Caption' => $model_factory->getObject('pm_Task')->getDisplayName()
                ),
                array (
                        'entityId' => 'pm_Function',
                        'ReferenceName' => 'pm_Function',
                        'Caption' => $model_factory->getObject('pm_Function')->getDisplayName()
                ),
        ));
    }
}