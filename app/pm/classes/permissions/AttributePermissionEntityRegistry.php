<?php

class AttributePermissionEntityRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array (
                        'entityId' => 'pm_ChangeRequest',
                        'ReferenceName' => 'pm_ChangeRequest',
                        'Caption' => getFactory()->getObject('pm_ChangeRequest')->getDisplayName()
                ),
                array (
                        'entityId' => 'pm_Task',
                        'ReferenceName' => 'pm_Task',
                        'Caption' => getFactory()->getObject('pm_Task')->getDisplayName()
                ),
                array (
                        'entityId' => 'pm_Function',
                        'ReferenceName' => 'pm_Function',
                        'Caption' => getFactory()->getObject('pm_Function')->getDisplayName()
                ),
        ));
    }
}