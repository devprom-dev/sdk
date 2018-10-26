<?php
/*
 * DEVPROM (http://www.devprom.net)
 * notifications.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 //////////////////////////////////////////////////////////////////////////////////////////////
 class DownloadsList extends PageList
 {
 	function DownloadsList( $object )
 	{
 		parent::PageList( $object );
 	}
 	
 	function getIterator()
 	{
 		$object = $this->getObject();
 		return $object->getAllForAdmin();
 	}
 	
	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
	
	function IsNeedToDisplay( $attr_it )
	{
		return false;
	}

	function drawCell( $object_it, $attr_it )
	{
		return parent::drawCell( $object_it, $attr_it );
	}

	function getUserColumns() 
	{
		return array('Файл', 'Пользователь');
	}

	function drawUserColumn( $column_name, $object_it ) 
	{
		if ( $column_name == 'Файл' )
		{
			echo $object_it->get('FileName').' (загрузок: '.$object_it->get('Downloads').')';
		}

		if ( $column_name == 'Пользователь' )
		{
			$user_it = $object_it->getUserIt();
			while ( !$user_it->end() )
			{
				echo '<div class="line">';
					echo $user_it->getRefLink();
					echo ' ('.$user_it->getDateTimeFormat('DownloadDate').', '.$user_it->get('Email').')';
				echo '</div>';
				
				$user_it->moveNext();
			}
		}
	} 
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class DownloadsTable extends ViewTable
 {
	function getObject()
	{
		global $model_factory;
 		return $model_factory->getObject('pm_DownloadAction');
	}
	
	function getList()
	{
		return new DownloadsList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Загрузки');
 	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class DownloadsPage extends Page
 {
 	function getTable() {
 		return new DownloadsTable();
 	}

 	function getForm() 
 	{
 		return null;
 	}
 }

?>