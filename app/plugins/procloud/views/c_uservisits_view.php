<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_uservisists_view.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 //////////////////////////////////////////////////////////////////////////////////////////////
 class UsersVisitsList extends ListTable
 {
 	function getIterator() 
 	{
 		global $project_it;
 		
		return $this->object->getLastVisitsOnProject( $project_it->getId() );
	}
	
	function IsNeedToDisplay( $attr_it ) 
	{
		switch( $attr_it->get('ReferenceName') ) 
		{
			case 'Caption': 
				return true;
		}
		return false;
	}

	function IsNeedToDisplayLinks( ) { return false; }
	function IsNeedToDelete( ) { return false; }
	function IsNeedToDisplayOperations( ) { return false; }

	function getUserColumns() {
		return array('Дата последнего визита', 'Количество визитов');
	}

	function drawUserColumn( $column_name, $object_it ) 
	{
		switch ( $column_name )
		{
			case 'Дата последнего визита':
				echo $object_it->getDateTimeFormat('LastVisit');
				break;

			case 'Количество визитов':
				echo $object_it->get('VisitsAmount');
				break;
		}
	}
	
	function drawCell( $object_it, $attr_it )
	{
		if($attr_it->get('ReferenceName') == 'Caption') 
		{
			$configuration = getConfiguration();
			
			if ( $configuration->hasTeams() )
			{
				echo '<a href="/profile/'.$object_it->getId().'">'.$object_it->getDisplayName().'</a>';
			}
			else
			{
				echo $object_it->getDisplayName();
			}
		}
	}
	
	function getUrl() {
		return $this->getPageUrl(0);
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class UsersVisitsTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('cms_User');
	}
	
	function getList()
	{
		return new UsersVisitsList( $this->object );
	}

	function getCaption() {
		return translate('Визиты пользователей');
	}
	
	function IsNeedToAdd() {
		return false; 
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////
 class UsersVisitsPage extends Page
 {
 	function UsersVisitsPage()
 	{
 		parent::Page();
 	}
 	
 	function getTable() {
 		return new UsersVisitsTable();
 	}
 	
 	function getForm() {
 		return null;
 	}
 }

?>