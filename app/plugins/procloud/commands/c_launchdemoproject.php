<?php

use Devprom\ApplicationBundle\Service\CreateProjectService;

class LaunchDemoProject extends CommandForm
{
	private $template_it = null;
	
 	function validate()
 	{
 		if ( $_REQUEST['template'] == '' ) $this->replyError(text('procloud1001'));
 		
 		$template = new Metaobject('pm_ProjectTemplate');
 		
 		$template->setRegistry( new ObjectRegistrySQL() );
 		
 		$this->template_it = 
 			$template->getRegistry()->Query(
 					array (
 							new FilterAttributePredicate('FileName', $_REQUEST['template'])
 					)
 			);
 		
 		if ( $this->template_it->getId() < 1 ) $this->replyError(text('procloud1001'));
 		
		return true;
 	}
 	
 	function create()
	{
		getSession()->close();
		
		getSession()->open($this->createDummyUser());
		
		$project_it = $this->createProject();
		
		if ( $project_it->getId() < 1 ) $this->replyError(text('procloud1002'));
		
		$project_it->modify( 
				array (
						'Platform' => 'demo'
				)
		);
		
		$this->replyRedirect('http://projectscloud.ru/pm/'.$project_it->get('CodeName'));
	}
	
	public function createDummyUser()
	{
		$user = getFactory()->getObject('User');
		
		return $user->getExact($user->add_parms(
				array (
						'Caption' => translate('Пользователь'),
						'Login' => $this->gen_uuid(),
						'Email' => $this->gen_uuid(),
						'Password' => $this->gen_uuid()
				)
		));
	}
	
	public function createProject()
	{
		$_REQUEST['Codename'] = $this->gen_uuid_short();
		$_REQUEST['Caption'] = $this->template_it->getDisplayName();
		$_REQUEST['Template'] = $this->template_it->getId();  
		$_REQUEST['User'] = getSession()->getUserIt()->getId();

		$service = new CreateProjectService();
		
		$code = $service->execute();
		
		if ( $code < 0 )
		{
			Logger::getLogger('Commands')->error('Unable to create demo project: '.$code);
		}
		
		return getFactory()->getObject('Project')->getExact($code);
	}
	
	function gen_uuid()
	{
		list($usec, $sec) = explode(" ",microtime());
		
		return md5(strftime('%d.%m.%Y.%M.%H.%S').((float)$usec + (float)$sec).rand());
	}

	function gen_uuid_short()
	{
		list($usec, $sec) = explode(" ",microtime());
		
		return base_convert(strftime('%d.%m.%Y.%M.%H.%S').((float)$usec + (float)$sec).rand(), 10, 36);
	}
}
