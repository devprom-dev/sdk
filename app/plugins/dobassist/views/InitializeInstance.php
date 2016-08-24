<?php

use Devprom\ApplicationBundle\Service\CreateProjectService;

include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';
include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";

class InitializeInstance extends Page
{
	private $trial_length = '14';
	private $language = 2;

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
		$this->language = $_REQUEST['l'] == 'ru' ? 1 : 2;

		$this->setupLoggers();

 		if ( getFactory()->getObject('User')->getAll()->count() > 0 ) return;
 		$log = 'License given: '.$this->createLicense().PHP_EOL;
 		
 		$user_id = $this->createUser( 
 				$_REQUEST['username'], $_REQUEST['userlogin'], $_REQUEST['useremail']
		);
 		
 		$user_it = getFactory()->getObject('User')->getExact($user_id);
 		$log .= 'User created: '.$user_id;
 		
 		$this->updateSystemSettings();
 		
 		unlink( $this->getKeyFile() );
		getCheckpointFactory()->getCheckpoint( 'CheckpointSystem' )->executeDynamicOnly();
		
		$this->setupBackgroundTasks();
		
		getSession()->close();
		getSession()->open($user_it);

		file_put_contents(dirname(SERVER_ROOT_PATH).'/initialize.log', $log);
		
		$this->setupDemoProject();
		$this->sendMail($user_it);

		$installation_factory = InstallationFactory::getFactory();
		$clear_cache_action = new ClearCache();
		$clear_cache_action->install();
        PluginsFactory::Instance()->invalidate();

		exit(header('Location: /pm/project-portfolio-1/issues/board/issuesboardcrossproject'));
	}
 	
 	protected function createUser( $name, $login, $email )
 	{
 		$user = getFactory()->getObject('User');
 		$user->setNotificationEnabled(false);
 		return $user->add_parms(
 				array (
 						'Caption' => $name,
 						'Login' => $login,
 						'Email' => $email,
 						'Password' => $login,
 						'Language' => $this->language,
 						'IsAdmin' => 'Y'
 				)
 		);

        $generator = new UserPicSpritesGenerator();
        $generator->storeSprites();
 	}
 	
 	protected function createLicense()
 	{
 		$key_file = $this->getKeyFile();
 		if ( file_exists($key_file) ) include $key_file;
 		
 		getFactory()->getObject('LicenseInstalled')->getAll()->modify(
 				array (
 						'LicenseType' => 'LicenseDevOpsBoard',
 						'LicenseValue' => $this->trial_length,
 						'LicenseKey' => $key_value
 				)
 		);
		file_put_contents(SERVER_ROOT_PATH.'/conf/license.dat', serialize(array('leftdays' => $this->trial_length)));

 		return $key_value;
 	}
 	
 	protected function updateSystemSettings()
 	{
		DAL::Instance()->Query("INSERT INTO pm_ProjectTemplate( OrderNum, Caption, Description, FileName, Language, ProductEdition, Kind) VALUES (70, 'text(co50)', 'text(co51)', 'incidents_ru.xml', 1, 'ee', 'case')");

 		getFactory()->getObject('cms_SystemSettings')->getAll()->modify(
 				array (
 						'Caption' => 'DevOps Board',
 						'EmailSender' => 'admin',
 						'AdminEmail' => SAAS_SENDER,
 						'ServerName' => EnvironmentSettings::getServerName(),
 						'ServerPort' => SAAS_SCHEME == 'http' ? 80 : 443,
 						'Language' => $this->language
 				)
 		);

		if ( $this->language == 2 ) {
			$allowed_templates = array (
				'ticket_en.xml',
				'kanban_en.xml',
				'incidents_en.xml'
			);
		}
		else {
			$allowed_templates = array (
				'ticket_ru.xml',
				'kanban_ru.xml',
				'incidents_ru.xml'
			);
		}

		$template = getFactory()->getObject('pm_ProjectTemplate');
		$template->setRegistry( new ObjectRegistrySQL() );
 		
		$template_it = $template->getRegistry()->Query();
		while( !$template_it->end() )
		{
			if ( in_array($template_it->get('FileName'), $allowed_templates) ) {
				$template_it->moveNext();
				continue;
			}
			
			$template_it->delete();
			$template_it->moveNext();
		}
		
		$this->translateDictionaries();
 	}
 	
 	protected function translateDictionaries()
 	{
 		$entities = array (
 				'pm_Importance',
 				'Priority',
 				'pm_ProjectRole',
 				'pm_TaskType',
 				'pm_ChangeRequestLinkType',
 				'cms_Language'
 		);
 		
 		foreach( $entities as $entity )
 		{
 			$object = getFactory()->getObject($entity);
 			$object->setNotificationEnabled(false);
 			
 			$it = $object->getAll();
 			while( !$it->end() )
 			{
 				$object->modify_parms($it->getId(),
 						array (
 								'Caption' => translate($it->getHtmlDecoded('Caption')),
 								'BackwardCaption' => translate($it->getHtmlDecoded('BackwardCaption'))
 						)
 					);
 				$it->moveNext();
 			}
 		}
 	}
 	
 	protected function getKeyFile()
 	{
 		return dirname(__FILE__).'/key.php';
 	}
 	
 	protected function setupLoggers()
 	{
 		$default_path = '/var/log/devprom';
 		
		$local_dir = dirname(SERVER_ROOT_PATH).'/logs';
		mkdir($local_dir, 0755, true);
		
		$settings_file = DOCUMENT_ROOT.'conf/logger.xml';
		copy(SERVER_ROOT_PATH.'templates/config/logger-linux.xml', $settings_file);
		file_put_contents($settings_file, str_replace($default_path, $local_dir, file_get_contents($settings_file)));
 	}
 	
	protected function sendMail( $user_it )
	{
		$to_address = $user_it->get('Email');
		$user_name = $user_it->get('Login');
		$user_pass = $user_it->get('Login');
		$host_url = SAAS_SCHEME.'://'.EnvironmentSettings::getServerName();
		
	    $mail = new HtmlMailbox;
	    $mail->appendAddress($to_address);
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/dobassist/resources/en/greetings.html');
	    $body = preg_replace('/\%user_name\%/', $user_it->get('Caption'), preg_replace('/\%host_url\%/', $host_url, $body));
	    $body = preg_replace('/\%password\%/', $user_pass, preg_replace('/\%login\%/', $user_name, $body));
	    $body = preg_replace('/\%pass_url\%/', $host_url.'/reset?key='.$user_it->getResetPasswordKey(), $body);
		
	    $mail->setBody($body);
	    $mail->setSubject(text('dobassist46'));
	    $mail->setFrom(str_replace('%1', SAAS_SENDER, text('dobassist44')));
	    $mail->send();
	}
	
	protected function setupBackgroundTasks()
	{
		$instance_number = intval(trim(file_get_contents(SAAS_ROOT.'instances.dat'), ' '.chr(10).chr(13)));
		
		$hours = round($instance_number / 60, 0);
		$minutes = $instance_number % 60;
		
		$job_it = getFactory()->getObject('co_ScheduledJob')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('ClassName', 
								array(
										'processbackup',
										'processcheckpoints',
										'trackhistory'
								)
					    )
				)
		);
		
		while( !$job_it->end() )
		{
			switch($job_it->get('ClassName'))
			{
			    case 'processbackup':
			    	$modify_hours = $hours < 1 ? 23 : min($hours, 23);
			    	break;
			    	
			    case 'processcheckpoints':
			    case 'trackhistory':
			    	$modify_hours = '*';
			    	break;
			}
			
			$job_it->modify(
					array (
							'Minutes' => min(max($minutes, 0), 59),
							'Hours' => $modify_hours
					)
			);
			
			$job_it->moveNext();
		} 
		
		$info_path = DOCUMENT_ROOT.'conf/runtime.info';

		$file = fopen( $info_path, 'w', 1 );
		fwrite( $file, time() );
		fclose( $file );
	}
	
	protected function setupDemoProject()
	{
		$service = new CreateProjectService();
		$project = getFactory()->getObject('Project');
		$template = getFactory()->getObject('pm_ProjectTemplate');
		$template->setRegistry( new ObjectRegistrySQL() );
		
		$support_it = $service->execute(
						array(
								'CodeName' => 'supportA',
								'Caption' => $this->language == 2 ? 'Support' : 'Поддержка',
								'Template' => $template->getRegistry()->Query(
													array (new FilterAttributePredicate('FileName', $this->language == 2 ? 'ticket_en.xml' : 'ticket_ru.xml'))
												)->getId(),
								'User' => getSession()->getUserIt()->getId(),
								'DemoData' => true
						)
			);

		$incidents_it = $service->execute(
						array(
								'CodeName' => 'incidentsA',
								'Caption' => $this->language == 2 ? 'Monitoring' : 'Мониторинг',
								'Template' => $template->getRegistry()->Query(
													array (new FilterAttributePredicate('FileName', $this->language == 2 ? 'incidents_en.xml' : 'incidents_ru.xml'))
												)->getId(),
								'User' => getSession()->getUserIt()->getId(),
								'DemoData' => true
						)
			);

		$program_it = $service->execute(
						array(
								'CodeName' => 'dev',
								'Caption' => $this->language == 2 ? 'Development' : 'Разработка',
								'Template' => $template->getRegistry()->Query(
													array (new FilterAttributePredicate('FileName', $this->language == 2 ? 'kanban_en.xml' : 'kanban_ru.xml'))
												)->getId(),
								'User' => getSession()->getUserIt()->getId(),
								'DemoData' => true
						)
			);


		$portfolio = getFactory()->getObject('co_ProjectGroup');
		$group_id = $portfolio->add_parms(
			array (
				'Caption' => $this->language == 2 ? 'Brand New Product' : 'Новый продукт'
			)
		);
		$portfolioLink = getFactory()->getObject('co_ProjectGroupLink');
		foreach( array($support_it->getId(), $program_it->getId(), $incidents_it->getId()) as $project_id ) {
			$portfolioLink->add_parms(
				array (
					'ProjectGroup' => $group_id,
					'Project' => $project_id
				)
			);
		}

		$service->invalidateCache();
		$service->invalidateServiceDeskCache();

		return $program_it;
	}
}
