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
		
		$this->setupProjectTemplates();
		
		getSession()->close();
		
		getSession()->open($user_it);

		file_put_contents(dirname(SERVER_ROOT_PATH).'/initialize.log', $log);
		
		if ( $_REQUEST['template'] != '' )
		{
			$project_it = $this->setupDemoProject($_REQUEST['template']);
		}

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
 		$user_id = getFactory()->getObject('User')->add_parms(
 				array (
 						'Caption' => $name,
 						'Login' => $login,
 						'Email' => $email,
 						'Password' => $login,
 						'IsAdmin' => 'Y'
 				)
 		);
 		
		$group_it = getFactory()->getObject('co_UserGroup')->getRegistry()->getAll();
		
		if ( $group_it->getId() > 0 )
		{
			getFactory()->getObject('co_UserGroupLink')->add_parms( 
					array (
							'SystemUser' => $user_id,
							'UserGroup' => $group_it->getId()
					)
			); 
		}
 		
 		return $user_id;
 	}
 	
 	protected function createLicense()
 	{
 		$key_file = $this->getKeyFile();
 				
 		if ( file_exists($key_file) ) include $key_file;
 		
 		getFactory()->getObject('LicenseInstalled')->getAll()->modify(
 				array (
 						'LicenseType' => 'LicenseSAASALM',
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
 						'Caption' => 'Devprom',
 						'EmailSender' => 'admin',
 						'AdminEmail' => SAAS_SENDER,
 						'ServerName' => EnvironmentSettings::getServerName(),
 						'ServerPort' => 443
 				)
 		);
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
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/saasassist/resources/greetings.html');
	    
	    $body = preg_replace('/\%host\%/', preg_replace('/\%user_name\%/', $user_it->get('Caption'), preg_replace('/\%1/', $host_url, text('saasassist22'))), $body);
	    $body = preg_replace('/\%account\%/', preg_replace('/\%2/', $user_pass, preg_replace('/\%1/', $user_name, text('saasassist23'))), $body);
	    $body = preg_replace('/\%links\%/', text('saasassist24'), $body);
	    $body = preg_replace('/\%trial\%/', text('saasassist25'), $body);
	    $body = preg_replace('/\%pass_url\%/', $host_url.'/reset?key='.$user_it->getResetPasswordKey(), $body);
		
	    $mail->setBody($body);
	    
	    $mail->setSubject( 'Начало работы в Devprom.ALM' );
	    $mail->setFrom("Devprom Software <".SAAS_SENDER.">");
	    	
	    $mail->send();
	}
	
	protected function setupBackgroundTasks()
	{
		$instance_number = intval(trim(file_get_contents('/home/saas/instances.dat'), ' '.chr(10).chr(13)));
		
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
	
	protected function setupProjectTemplates()
	{
		$demo_templates = array (
		);
		
		$project_tpl = getFactory()->getObject('pm_ProjectTemplate');
		
		foreach( $demo_templates as $file_name => $template_title )
		{
			$project_tpl->add_parms( 
					array (
							'Caption' => $template_title,
							'FileName' => $file_name,
							'Language' => 1,
							'ProductEdition' => 'demo'
					)
			);
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
		
		if ( $template_it->getId() < 1 )
		{
			return getFactory()->getObject('Project')->getEmptyIterator();
		}
		
		$parms = array();
		
		$parms['CodeName'] = 'project1';
		$parms['Caption'] = $template_it->getHtmlDecoded('Caption');
		$parms['Template'] = $template_it->getId();  
		$parms['User'] = getSession()->getUserIt()->getId();
		$parms['DemoData'] = true;

		$service = new CreateProjectService();
		
		$project_it = getFactory()->getObject('Project')->getExact($service->execute($parms));
		
		$project_it->modify(
				array(
						'Platform' => 'demo'
				)
		);
		
		return $project_it;
	}
}
