<?php

include SERVER_ROOT_PATH."admin/classes/maintenance/BackupAndRecoveryOnWindows.php";

include "BackupTable.php";
include "BackupFormDatabase.php";
include "BackupFormApplication.php";
include "BackupFormComplete.php";
include "RecoveryWizardFormBase.php";
include "RecoveryFormUnpack.php";
include "RecoveryFormFiles.php";
include "RecoveryFormApplication.php";

class BackupPage extends AdminPage
{
	function getObject() {
		return getFactory()->getObject('Backup');
	}
	
	function getTable() {
		return new BackupTable($this->getObject());
	}

	function needDisplayForm() {
	    return $_REQUEST['action'] != '';
	}
	
	function getEntityForm()
	{
	    $object = getFactory()->getObject('cms_Update');
	    switch ( $_REQUEST['action'] )
	    {
	        case 'backupdatabase':
	            return new BackupFormDatabase( $object );

            case 'backupapplication':
                return new BackupFormApplication( $object );

            case 'backupcomplete':
                return new BackupFormComplete( $object );

            case 'recoveryunpack':
                return new RecoveryFormUnpack( $object );
                
            case 'recoveryfiles':
                return new RecoveryFormFiles( $object );
            
            case 'recoveryapplication':
                return new RecoveryFormApplication( $object );
                
	        default:
	            return null;
	    }
	}
	
	function export()
	{
	    switch ( $_REQUEST['export'] )
	    {
	        case 'download':
	            $backupIt = $this->getObject()->getAll();
                $backupIt->moveToId($_REQUEST['backup_file_name']);

                if ( $backupIt->getId() != '' ) {
                    $downloader = new Downloader;
                    $downloader->echoFile(
                        SERVER_BACKUP_PATH.$backupIt->get('Caption'),
                        $backupIt->get('Caption'),
                        'application/zip'
                    );
                }
	            break;

	        default:
	            parent::export();
	    }
	}
}
