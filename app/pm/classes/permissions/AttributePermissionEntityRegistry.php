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
            array (
                'entityId' => 'pm_Milestone',
                'ReferenceName' => 'pm_Milestone',
                'Caption' => getFactory()->getObject('pm_Milestone')->getDisplayName()
            ),
            array (
                'entityId' => 'pm_Release',
                'ReferenceName' => 'pm_Release',
                'Caption' => getFactory()->getObject('pm_Release')->getDisplayName()
            ),
            array (
                'entityId' => 'pm_Version',
                'ReferenceName' => 'pm_Version',
                'Caption' => getFactory()->getObject('pm_Version')->getDisplayName()
            ),
        ));
    }
}