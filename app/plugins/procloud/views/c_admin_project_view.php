<?php
/*
 * DEVPROM (http://www.devprom.net)
 * teams.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 //////////////////////////////////////////////////////////////////////////////////////////////
 class ProjectsList extends PageList
 {
 	function getIterator()
 	{
 		return $this->object->getAllPublicIt();
 	}
 	
 	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}

	function getItemActions( $column_name, $object_it ) 
	{
		global $session, $methodology_it;
		
		$actions = parent::getItemActions( $column_name, $object_it );
		
		array_push( $actions,
			array( '/admin/module/procloud/projects?project='.$object_it->getId(), translate('Убрать из каталога')) );
		
		return $actions;
	}	
	
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr ) 
		{
			case 'Caption':	
			case 'CodeName':	
			case 'Description':	
			case 'Rating':	
				return true;
				
			default:
				return false;
		}
		
		return parent::IsNeedToDisplay($attr);
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class ProjectsTable extends PageTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('pm_Project');
	}
	
	function getList()
	{
		return new ProjectsList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Публичные проекты');
 	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class ProjectsPage extends Page
 {
 	function ProjectsPage()
 	{
 		global $_REQUEST, $model_factory;
 		
 		if ( $_REQUEST['project'] != '' )
 		{
 			$public = $model_factory->getObject('pm_PublicInfo');
 			$public_it = $public->getByRef('Project', $_REQUEST['project']);
 			
 			if ( $public_it->count() > 0 )
 			{
 				$public_it->modify ( array('IsProjectInfo' => 'N') );
 			}
 			
 			exit(header('Location: /admin/module/procloud/projects'));
 		}
 		
 		parent::Page();
 	}
 	
 	function getTable() 
 	{
 		return new ProjectsTable();
 	}

 	function getForm() 
 	{
 		global $model_factory, $_REQUEST;
 		
 		if ( $_REQUEST['entity'] == 'pm_Project' )
 		{
	 		return new MetaObjectForm(
	 			$model_factory->getObject('pm_Project'));
 		}
 	}
 }
?>