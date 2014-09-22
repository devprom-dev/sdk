<?php

include_once "MaintenanceCommand.php";
include SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class BackupComplete extends MaintenanceCommand
{
	function validate()
	{
		return true;
	}

	function create()
	{
	    global $_REQUEST, $model_factory;

	    $configuration = getConfiguration();
	    
	    $backup = $configuration->getBackupAndRecoveryStrategy();

	    $backup->setBackupName($backup->getBackupName());
	    
	    $result = $backup->zip();
	    	
	    $parts = preg_split('/,/', $_REQUEST['parms']);
	    
	    $redirect_url = '?';
	    
	    switch ( $parts[0] )
	    {
	        case 'project':
	            
	            $project = $model_factory->getObject('pm_Project');
	            
	            $project_it = $project->getExact( preg_split('/-/', $parts[1]) );
	            	
	            $reason = str_replace('%1', join(',',$project_it->fieldToArray('Caption')), text(1172));
	            
	            $redirect_url = '/admin/command.php?class=projectremove&project='.join('-',$project_it->idsToArray());
	            
	            break;
	            	
	        case 'update':
	            
	            $reason = str_replace('%1', $parts[1], text(1174));
	            
	            $redirect_url = '/admin/updates.php?action=updateapplication&parms='.$parts[1];
	            
	            break;
	            
	        default:
	            $reason = text(1304);
	    }
	    	
	    $backup_cls = $model_factory->getObject('cms_Backup');
	    
	    $backup_cls->add_parms( array (
	            'Caption' => $reason,
	            'BackupFileName' => $backup->getBackupFileName()
	    ));
	     
		$this->replyRedirect( $redirect_url );
	}
}
