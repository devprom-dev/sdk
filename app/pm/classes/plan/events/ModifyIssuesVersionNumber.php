<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";

class ModifyIssuesVersionNumber extends ObjectFactoryNotificator
{
    function add( $object_it ) {
    }

    function modify( $prev_object_it, $object_it )
    {
        if ($object_it->object instanceof Release) {
            if ( $object_it->get('Caption') == $prev_object_it->get('Caption') ) return;
            $was_data = $this->getWasData();
            $this->handleRelease($object_it, $was_data['Caption']);
        }
        if ($object_it->object instanceof Iteration) {
            if ( $object_it->get('ReleaseNumber') == $prev_object_it->get('ReleaseNumber') && $object_it->get('Version') == $prev_object_it->get('Version') ) return;
            $was_data = $this->getWasData();
            $release_it = getFactory()->getObject('Release')->getExact($was_data['Version']);
            $was_version = $release_it->getDisplayName().'.'.$was_data['ReleaseNumber'];
            $this->handleIteration($object_it, $was_version);
        }
    }

    function delete( $object_it )
    {
        if ($object_it->object instanceof Release) {
            $this->clearVersion($object_it->getDisplayName());
        }
        if ($object_it->object instanceof Iteration) {
            $this->clearVersion($object_it->getDisplayName());
        }
    }

    protected function updateVersion( $object_it, $version )
    {
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET SubmittedVersion = '".$object_it->getDisplayName()."' WHERE SubmittedVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET ClosedInVersion = '".$object_it->getDisplayName()."' WHERE ClosedInVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_Test SET Version = '".$object_it->getDisplayName()."' WHERE Version = '".$version."'");
    }

    protected function clearVersion( $version ) {
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET SubmittedVersion = NULL WHERE SubmittedVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET ClosedInVersion = NULL WHERE ClosedInVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_Test SET Version = NULL WHERE Version = '".$version."'");
    }
}