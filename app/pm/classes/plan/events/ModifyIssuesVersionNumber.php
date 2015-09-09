<?php
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class ModifyIssuesVersionNumber extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1)
	{
        if ($kind != TRIGGER_ACTION_MODIFY) return;

        if ($object_it->object instanceof Release && array_key_exists('Caption', $content)) {
            $this->handleRelease($object_it);
        }
        if ($object_it->object instanceof Iteration) {
            if ( array_key_exists('ReleaseNumber', $content) || array_key_exists('Version', $content) ) {
                $this->handleIteration($object_it);
            }
        }
	}

    protected function handleRelease( $object_it )
    {
        $was_data = $this->getWasData();
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET SubmittedVersion = '".$object_it->getDisplayName()."' WHERE SubmittedVersion = '".$was_data['Caption']."'");
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET ClosedInVersion = '".$object_it->getDisplayName()."' WHERE ClosedInVersion = '".$was_data['Caption']."'");
    }

    protected function handleIteration( $object_it )
    {
        $was_data = $this->getWasData();
        $release_it = getFactory()->getObject('Release')->getExact($was_data['Version']);
        $was_version = $release_it->getDisplayName().'.'.$was_data['ReleaseNumber'];

        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET SubmittedVersion = '".$object_it->getDisplayName()."' WHERE SubmittedVersion = '".$was_version."'");
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET ClosedInVersion = '".$object_it->getDisplayName()."' WHERE ClosedInVersion = '".$was_version."'");
    }
}