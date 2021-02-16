<?php
include_once "MaintenanceCommand.php";
include SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class BackupComplete extends MaintenanceCommand
{
	function validate() {
		return true;
	}

	function create()
	{
	    $backup = getConfiguration()->getBackupAndRecoveryStrategy();
	    $backup->setBackupName($backup->getBackupName());

	    try {
            $backup->zip();
            DAL::Instance()->Reconnect();
        }
        catch( \Exception $e ) {
	        $this->replyError($e->getMessage());
        }

        $redirect_url = '?';

	    $parts = preg_split('/,/', $_REQUEST['parms']);
	    switch ( $parts[0] )
	    {
	        case 'project':
	            $project = getFactory()->getObject('pm_Project');
	            $project_it = $project->getExact(\TextUtils::parseIds($parts[1]));
	            	
	            $reason = str_replace('%1', join(',',$project_it->fieldToArray('Caption')), text(1172));
	            $redirect_url = '/admin/command.php?class=projectremove&project='.join('-',$project_it->idsToArray());
	            break;
	            	
	        case 'update':
	            $reason = str_replace('%1', $parts[1], text(1174));
	            $redirect_url = '/admin/updates.php?action=updateapplication&parms='.$parts[1];
	            break;
	            
	        default:
                $backup->backup_files();
                DAL::Instance()->Reconnect();

	            $reason = text(1304);
	    }
	    	
	    $backup_cls = getFactory()->getObject('cms_Backup');
	    
	    $backup_cls->add_parms( array (
	            'Caption' => $reason,
	            'BackupFileName' => $backup->getBackupFileName()
	    ));
	     
		$this->replyRedirect( $redirect_url );
	}
}
