<?php

 ///////////////////////////////////////////////////////////////////////////////////////
 class BuildWebMethod extends WebMethod
 {
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class BuildNotifyWebMethod extends BuildWebMethod
 {
 	function getCaption()
 	{
 		return translate('Уведомить о сборке');
 	}

 	function execute_request()
 	{
 		global $model_factory, $_REQUEST, $project_it;
 		
 		$parms = $_REQUEST;
 		
		$settings = $model_factory->getObject('cms_SystemSettings');
 		$settings_it = $settings->getAll();

 		$build = $model_factory->getObject('pm_Build');
 		$build_it = $build->getExact($parms['build']);
 		
 		if ( $build_it->count() < 1 )
 		{
 			return;
 		}

   		$mail = new Mailbox;
   		$part_it = $project_it->getParticipantIt();
   		
   		for( $i = 0; $i < $part_it->count(); $i++)
   		{
   			$mail->appendAddress($part_it->get('Email'));
   			$part_it->moveNext();
   		}

		$body = translate('Выпущена новая сборка').': '.
			$build_it->getFullNumber().Chr(10).Chr(10);

		$body .= translate('Состав сборки доступен по ссылке').' '.
			_getServerUrl().'/pm/'.$project_it->get('CodeName').'/issues/list/resolved?build='.$build_it->getId();
		
   		$mail->setBody($body);
   		$mail->setSubject('['.$project_it->get('CodeName').']: '.translate('Новая сборка'));
   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
		$mail->send();
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class BuildSelectWebMethod extends SelectWebMethod
 {
 	var $release_it;
 	
 	function BuildSelectWebMethod( $release_it = null )
 	{
 		$this->release_it = $release_it;
 		parent::SelectWebMethod();
 	}
 	
 	function getValues()
 	{
		global $model_factory;
 		
		$build = $model_factory->getObject('pm_Build');
		$build_it = $build->getByRef('Release', $this->release_it->getId());
		
		$builds = array( '' => 0 );
		for($i = 0; $i < $build_it->count(); $i++)
		{
			$builds = array_merge( $builds,  
				array( $build_it->getFullNumber() => $build_it->getId()) );
			
			$build_it->moveNext();
		} 	
		
		return array_flip($builds);	
 	}
 	
 	function execute( $parms_array, $value )
 	{
 		// id of task is currently reasigned to build with $value id
 		$task_id = $parms_array['task_id'];
 		
 		if($task_id > 0)
 		{
 			global $model_factory;
			$buildtask = $model_factory->getObject('pm_BuildTask');

			// get the existing relation between the task and a build 			
 			$buildtask_it = $buildtask->getByRef('Task', $task_id);
 			
 			// remove previous relations between the task and the builds
 			if($buildtask_it->count() > 0) {
 				$buildtask->delete($buildtask_it->getId());
 			}

 			if($value > 0) {
 				// if the user has selected the build number
 				// then assign the task to it
 				$buildtask->add_parms(
 					array( 'Build' => $value, 'Task' => $task_id)
 					);
 			}
 		}
 	}
 }
  
 ///////////////////////////////////////////////////////////////////////////////
?>
