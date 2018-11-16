<?php

class AttributePermissionEntityRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        $data = array (
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
            array (
                'entityId' => 'pm_Activity',
                'ReferenceName' => 'pm_Activity',
                'Caption' => getFactory()->getObject('Activity')->getDisplayName()
            ),
        );

        if ( getSession()->getProjectIt()->getMethodologyIt()->get('IsRequirements') == ReqManagementModeRegistry::RDD ) {
            if ( class_exists('Issue') ) {
                $data[] = array (
                    'entityId' => 'Issue',
                    'ReferenceName' => 'Issue',
                    'Caption' => getFactory()->getObject('Issue')->getDisplayName()
                );
            }
            if ( class_exists('Increment') ) {
                $data[] = array (
                    'entityId' => 'pm_ChangeRequest',
                    'ReferenceName' => 'pm_ChangeRequest',
                    'Caption' => getFactory()->getObject('Increment')->getDisplayName()
                );
            }
        }
        else {
            $data[] = array (
                'entityId' => 'pm_ChangeRequest',
                'ReferenceName' => 'pm_ChangeRequest',
                'Caption' => getFactory()->getObject('pm_ChangeRequest')->getDisplayName()
            );
        }

        return $this->createIterator($data);
    }
}