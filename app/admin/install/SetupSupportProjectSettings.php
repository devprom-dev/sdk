<?php
include_once SERVER_ROOT_PATH."pm/classes/settings/Methodology.php";

class SetupSupportProjectSettings extends Installable 
{
    function check()
    {
        return true;
    }

	function skip()
	{
		return getFactory()->getObject('User')->getRegistry()->Count() < 1;
	}
    
    function install()
    {
		$serviceDeskSettingsFile = SERVER_ROOT_PATH.'co/bundles/Devprom/ServiceDeskBundle/Resources/config/settings.yml';

		$settings_file = file_get_contents($serviceDeskSettingsFile);
    	if ( preg_match('/supportProjectId:\s*([\d]+)/i', $settings_file, $result) )
    	{
    		if ( $result[1] != '' ) {
		    	$project_it = getFactory()->getObject('Methodology')->getRegistry()->Query(
			    			array (
			    					new FilterAttributePredicate('Project', array($result[1]))
			    			)
				   	);
		    	if ( $project_it->getId() != '' )
		    	{
		    		$project_it->object->addAttribute('IsSupportUsed', 'CHAR', '', true, true);
			    	$project_it->object->modify_parms($project_it->getId(), 
			    			array (
			    					'IsSupportUsed' => 'Y'
			    			)
			    	);
		    	}
    		}
    	}

		$applicationSettingsFile = SERVER_ROOT_PATH.'co/bundles/Devprom/ApplicationBundle/Resources/config/settings.yml';
		if ( !file_exists($applicationSettingsFile) ) {
			copy($serviceDeskSettingsFile, $applicationSettingsFile);
		}

        return true;
    }
}
