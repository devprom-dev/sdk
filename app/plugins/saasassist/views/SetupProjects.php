<?php

use Devprom\ApplicationBundle\Service\CreateProjectService;

class SetupProjects extends Page
{
	public function __construct()
	{
	}
	
 	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}

 	function render() 
 	{
 		if ( getFactory()->getObject('Project')->getAll()->count() > 0 ) return;

		if ( $_REQUEST['template'] != '' )
		{
			$project_it = $this->setupDemoProject($_REQUEST['template']);
		}

		if ( is_object($project_it) && $project_it->getId() > 0 )
		{
			exit(header('Location: /pm/'.$project_it->get('CodeName')));
		}
		else
		{
			exit(header('Location: /projects/welcome'));
		}
	}

	protected function setupDemoProject( $template_file_name )
	{
		$template = getFactory()->getObject('pm_ProjectTemplate');
		$template->setRegistry( new ObjectRegistrySQL() );
		
		$template_it = $template->getRegistry()->Query(
            array (
                new FilterAttributePredicate('FileName', $template_file_name)
            )
        );
		if ( $template_it->getId() < 1 ) {
			return getFactory()->getObject('Project')->getEmptyIterator();
		}
		
		$parms = array();
		
		$parms['CodeName'] = 'project1';
		$parms['Caption'] = translate('Демо-проект');
		$parms['Template'] = $template_it->getId();  
		$parms['User'] = getSession()->getUserIt()->getId();
		$parms['DemoData'] = true;

		$service = new CreateProjectService();
		$project_it = $service->execute($parms);
		$project_it->modify(
            array(
                'Platform' => 'demo'
            )
		);
		$service->invalidateCache();
		if ( $project_it->getMethodologyIt()->get('IsSupportUsed') == 'Y') {
			$service->invalidateServiceDeskCache();
		}

		return $project_it;
	}
}
