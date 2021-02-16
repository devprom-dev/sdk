<?php

class AttributePermissionEntityRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        $data = array (
            array (
                'entityId' => 'task',
                'ReferenceName' => 'pm_Task',
                'Caption' => getFactory()->getObject('pm_Task')->getDisplayName()
            ),
            array (
                'entityId' => 'feature',
                'ReferenceName' => 'pm_Function',
                'Caption' => getFactory()->getObject('pm_Function')->getDisplayName()
            ),
            array (
                'entityId' => 'milestone',
                'ReferenceName' => 'pm_Milestone',
                'Caption' => getFactory()->getObject('pm_Milestone')->getDisplayName()
            ),
            array (
                'entityId' => 'iteration',
                'ReferenceName' => 'pm_Release',
                'Caption' => getFactory()->getObject('pm_Release')->getDisplayName()
            ),
            array (
                'entityId' => 'release',
                'ReferenceName' => 'pm_Version',
                'Caption' => getFactory()->getObject('pm_Version')->getDisplayName()
            ),
            array (
                'entityId' => 'pm_Activity',
                'ReferenceName' => 'pm_Activity',
                'Caption' => getFactory()->getObject('Activity')->getDisplayName()
            ),
        );

        if ( getSession()->IsRDD() ) {
            if ( class_exists('Issue') ) {
                $data[] = array (
                    'entityId' => 'issue',
                    'ReferenceName' => 'Issue',
                    'Caption' => getFactory()->getObject('Issue')->getDisplayName()
                );
            }
            if ( class_exists('Increment') ) {
                $data[] = array (
                    'entityId' => 'request',
                    'ReferenceName' => 'pm_ChangeRequest',
                    'Caption' => getFactory()->getObject('Increment')->getDisplayName()
                );
            }
        }
        else {
            $data[] = array (
                'entityId' => 'request',
                'ReferenceName' => 'pm_ChangeRequest',
                'Caption' => getFactory()->getObject('pm_ChangeRequest')->getDisplayName()
            );
        }

        return $this->createIterator($data);
    }
}