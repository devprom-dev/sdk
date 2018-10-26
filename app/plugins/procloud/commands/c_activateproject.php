<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_activateproject.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 include( dirname(__FILE__).'/c_cocreateproject.php');
 include( dirname(__FILE__).'/c_cocreatesite.php');

 //////////////////////////////////////////////////////////////////////////////////////////
 class ActivateProject extends CreateProject
 {
 	function execute()
	{
		global $_REQUEST, $model_factory;
		
 		$model_factory->object_factory->access_policy = 
 			new AccessPolicy;

		$key = $_REQUEST['key'];
		if(!isset($key))
		{
			$this->replyError('Необходимо указать ключ активации'); 
		}

		// найдем информацию о создании проекта
		$prj_cr_cls = $model_factory->getObject('pm_ProjectCreation');
		$create_info = $prj_cr_cls->getByRef('CreationHash', $key);

		if( $create_info->count() < 1 ) 
		{
			$this->replyError('Указанный ключ активации не существует'); 
		}
		
		switch ( $create_info->get('Access') )
		{
			case 'private':
				$strategy = new ProjectActivationStrategy( $create_info );
				break;
				
			default:
				$strategy = new ProductSiteStrategy( $create_info );
		}
		
		$result = $strategy->execute();
		
		if( $result > 0 ) 
		{
			$this->replyRedirect('/pm/'.$create_info->get('CodeName'), 'Проект успешно создан'); 
		}
		else 
		{
			$this->replyError($this->getResultDescription( $result )); 
		}
	}
 }
 
?>