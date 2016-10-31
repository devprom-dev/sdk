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
            $was_data = $prev_object_it->getData();
            $this->updateVersion($object_it->getDisplayName(), $was_data['Caption']);
        }
        if ($object_it->object instanceof Iteration) {
            if ( $object_it->get('ReleaseNumber') == $prev_object_it->get('ReleaseNumber') && $object_it->get('Version') == $prev_object_it->get('Version') ) return;
            $was_data = $prev_object_it->getData();
            $this->updateVersion($object_it->get('ShortCaption'), $was_data['ShortCaption']);
        }
    }

    function delete( $object_it )
    {
        if ($object_it->object instanceof Release) {
            $this->clearVersion($object_it->getDisplayName());
            DAL::Instance()->Query("UPDATE pm_ChangeRequest SET PlannedRelease = NULL WHERE PlannedRelease = ".$object_it->getId());
        }
        if ($object_it->object instanceof Iteration) {
            $this->clearVersion($object_it->getDisplayName());
            DAL::Instance()->Query("UPDATE pm_Task SET `Release` = NULL WHERE `Release` = ".$object_it->getId());
        }
    }

    protected function updateVersion( $new_version, $version )
    {
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET SubmittedVersion = '".$new_version."' WHERE SubmittedVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET ClosedInVersion = '".$new_version."' WHERE ClosedInVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_Test SET Version = '".$new_version."' WHERE Version = '".$version."'");
    }

    protected function clearVersion( $version ) {
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET SubmittedVersion = NULL WHERE SubmittedVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_ChangeRequest SET ClosedInVersion = NULL WHERE ClosedInVersion = '".$version."'");
        DAL::Instance()->Query("UPDATE pm_Test SET Version = NULL WHERE Version = '".$version."'");
    }
}