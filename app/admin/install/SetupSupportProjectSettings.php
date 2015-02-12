<?php

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
    	$settings_file = file_get_contents(SERVER_ROOT_PATH.'co/bundles/Devprom/ServiceDeskBundle/Resources/config/settings.yml');
    	
    	if ( preg_match('/supportProjectId:\s*([\d]+)/i', $settings_file, $result) )
    	{
    		if ( $result[1] != '' )
    		{
		    	$project_it = getFactory()->getObject('Project')->getRegistry()->Query(
			    			array (
			    					new FilterInPredicate(array($result[1]))
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
        return true;
    }
}
