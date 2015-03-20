<?php

use Devprom\ApplicationBundle\Service\CreateProjectService;

include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';

class InitializeInstance extends Page
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
 		if ( getFactory()->getObject('User')->getAll()->count() > 0 ) return;
 		
 		$log = 'License given: '.$this->createLicense().PHP_EOL;
 		
 		$user_id = $this->createUser( 
 				$_REQUEST['username'], $_REQUEST['userlogin'], $_REQUEST['useremail']
		);
 		
 		$user_it = getFactory()->getObject('User')->getExact($user_id);
 		
 		$log .= 'User created: '.$user_id;
 		
 		$this->updateSystemSettings();
 		
 		unlink( $this->getKeyFile() );
 		
		$checkpoint_factory = getCheckpointFactory();
		$checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );
		$checkpoint->executeDynamicOnly();
		
		$this->sendMail($user_it);
		$this->setupLoggers();
		$this->setupBackgroundTasks();
		
		getSession()->close();
		getSession()->open($user_it);

		file_put_contents(dirname(SERVER_ROOT_PATH).'/initialize.log', $log);
		
		$project_it = $this->setupDemoProject();

		$installation_factory = InstallationFactory::getFactory();
		$clear_cache_action = new ClearCache();
		$clear_cache_action->install();

		if ( is_object($project_it) && $project_it->getId() > 0 )
		{
			exit(header('Location: /pm/'.$project_it->get('CodeName')));
		}
		else
		{
			exit(header('Location: /'));
		}
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
 						'Language' => 2,
 						'IsAdmin' => 'Y'
 				)
 		);
 	}
 	
 	protected function createLicense()
 	{
 		$key_file = $this->getKeyFile();
 		if ( file_exists($key_file) ) include $key_file;
 		
 		getFactory()->getObject('LicenseInstalled')->getAll()->modify(
 				array (
 						'LicenseType' => 'LicenseDevOpsBoard',
 						'LicenseValue' => '14',
 						'LicenseKey' => $key_value
 				)
 		);
 		
 		return $key_value;
 	}
 	
 	protected function updateSystemSettings()
 	{
 		getFactory()->getObject('cms_SystemSettings')->getAll()->modify(
 				array (
 						'Caption' => 'DevOps Board',
 						'EmailSender' => 'admin',
 						'AdminEmail' => SAAS_SENDER,
 						'ServerName' => EnvironmentSettings::getServerName(),
 						'ServerPort' => SAAS_SCHEME == 'http' ? 80 : 443,
 						'Language' => 2
 				)
 		);
 		
 		$allowed_templates = array (
 				'kanban_en.xml',
 				'ticket_en.xml',
 				'incidents_en.xml',
 				'scrum_en.xml'
 		);
 		
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
		file_put_contents($settings_file, str_replace($default_path, $local_dir, file_get_contents($settings_file))); 
 	}
 	
	protected function sendMail( $user_it )
	{
		$to_address = $user_it->get('Email');
		$user_name = $user_it->get('Login');
		$user_pass = $user_it->get('Login');
		$host_url = 'https://'.EnvironmentSettings::getServerName();
		
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
		
		$support_it = $project->getExact(
				$service->execute(
						array(
								'CodeName' => 'supportA',
								'Caption' => 'Support',
								'Template' => $template->getRegistry()->Query(array (new FilterAttributePredicate('FileName', 'ticket_en.xml')))->getId(),
								'User' => getSession()->getUserIt()->getId(),
								'DemoData' => true
						)
				)
		);

		$incidents_it = $project->getExact(
				$service->execute(
						array(
								'CodeName' => 'incidentsA',
								'Caption' => 'Incidents',
								'Template' => $template->getRegistry()->Query(array (new FilterAttributePredicate('FileName', 'incidents_en.xml')))->getId(),
								'User' => getSession()->getUserIt()->getId(),
								'DemoData' => true
						)
				)
		);

		$program_it = $project->getExact(
				$service->execute(
						array(
								'CodeName' => 'productA',
								'Caption' => 'Product',
								'Template' => $template->getRegistry()->Query(array (new FilterAttributePredicate('FileName', 'kanban_en.xml')))->getId(),
								'User' => getSession()->getUserIt()->getId(),
								'DemoData' => true
						)
				)
		);

		$link = getFactory()->getObject('ProjectLink');
		$link->add_parms(
				array (
						'Source' => $program_it->getId(),
						'Target' => $support_it->getId(),
						'LinkType' => 2,
						'Requests' => 1,
						'Tasks' => 1,
						'KnowledgeBase' => 3,
						'Blog' => 3,
						'SourceCode' => 1,
						'HelpFiles' => 1,
						'Testing' => 1,
						'Requirements' => 3
				)
		);
		$link->add_parms(
				array (
						'Source' => $program_it->getId(),
						'Target' => $incidents_it->getId(),
						'LinkType' => 2,
						'Requests' => 1,
						'Tasks' => 1,
						'KnowledgeBase' => 3,
						'Blog' => 3,
						'SourceCode' => 1,
						'HelpFiles' => 1,
						'Testing' => 1,
						'Requirements' => 3
				)
		);
		
		return $program_it;
	}
}
