<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_admin_stats_view.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class Statistics
 {
 	function getActiveProjects( $days = 30 ) 
 	{
 		global $model_factory;
 		
		$project = $model_factory->getObject('pm_Project');
		return $project->getActiveIt( $days ); 
	}

 	function getNotEmptyProjects(  ) 
 	{
 		global $model_factory;
 		
		$project = $model_factory->getObject('pm_Project');
		return $project->getNotEmptyIt(); 
	}

 	function getActiveUsers( $days = 30 ) 
 	{
 		global $model_factory;
 		
		$project = $model_factory->getObject('cms_User');
		return $project->getActiveIt( $days ); 
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class StatsList extends PageList
 {
 	var $stats;
 	
 	function StatsList( $object )
 	{
 		parent::PageList( $object );
 		$this->stats = new Statistics;
 	}
 	
	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
	
	function IsNeedToDelete() 
	{ 
		return false; 
	}

	function IsNeedToDisplayOperations() 
	{ 
		return false; 
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class StatsTable extends ViewTable
 {
	function IsNeedToAdd() 
	{
		return false;
	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////////////
 class ReportsTable extends ViewTable
 {
 	function ReportsTable()
 	{
 	}
 	
 	function draw()
 	{
 		echo '<div class="line">';
 		echo '<a href="?mode=activeprojects&days=30">' .
 				'<img style="margin-bottom:-3px;" border=0 src="/images/report.png"></a> ';
 		echo text('procloud21');
 		echo '</div>';

 		echo '<div class="line">';
 		echo '<a href="?mode=activeprojects&days=365">' .
 				'<img style="margin-bottom:-3px;" border=0 src="/images/report.png"></a> ';
 		echo text('procloud22');
 		echo '</div>';
 		
 		echo '<div class="line">';
 		echo '<a href="?mode=notemptyprojects">' .
 				'<img style="margin-bottom:-3px;" border=0 src="/images/report.png"></a> ';
 		echo text('procloud23');
 		echo '</div>';

 		echo '<div class="line">';
 		echo '<a href="?mode=activeusers&days=30">' .
 				'<img style="margin-bottom:-3px;" border=0 src="/images/report.png"></a> ';
 		echo text('procloud24');
 		echo '</div>';

 		echo '<div class="line">';
 		echo '<a href="?mode=activeusers&days=365">' .
 				'<img style="margin-bottom:-3px;" border=0 src="/images/report.png"></a> ';
 		echo text('procloud25');
 		echo '</div>';
 	}
 } 
  
 //////////////////////////////////////////////////////////////////////////////////////////////
 class ActiveProjectsList extends StatsList
 {
	function getIterator( ) 
	{ 
		global $_REQUEST;
		return $this->stats->getActiveProjects( $_REQUEST['days'] ); 
	}

	function IsNeedToDisplay( $attr_it )
	{
		 switch ( $attr_it->get('ReferenceName') )
		 {
		 	case 'Platform':
		 	case 'IsConfigurations':
		 	case 'MainWikiPage':
		 	case 'RequirementsWikiPage':
		 	case 'StartDate':
		 	case 'FinishDate':
		 	case 'Budget':
		 	case 'IsClosed':
		 	case 'Blog':
		 	case 'Tools':
		 		return false;
		 		
		 	default:
		 		return parent::IsNeedToDisplay( $attr_it );
		 }
	}

	function getUserColumns() 
	{
		return array('Дата последнего посещения', 'Участники');
	}

	function drawUserColumn( $column_name, $object_it ) 
	{
		if( $column_name == 'Дата последнего посещения' )
		{
			echo $object_it->getDateFormat('LastAccessed');
		}
		
		if( $column_name == 'Участники' )
		{
			$part_it = $object_it->getParticipantIt();
			
			while ( !$part_it->end() )
			{
				echo '<div>'.$part_it->getDisplayName().'</div>'; 
				$part_it->moveNext();
			}
		}
	} 
	
	function getMaxOnPage()
	{
		return 40;
	}
 }

 //////////////////////////////////////////////////////////////////////////////////////////////
 class ActiveProjectsTable extends StatsTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('pm_Project');
	}
	
	function getList()
	{
		return new ActiveProjectsList( $this->object );
	}

 	function getCaption()
 	{
 		return text('procloud21');
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////////////
 class ActiveUsersList extends StatsList
 {
 	function IsNeedToDisplay( $attr_it )
 	{
 		if ( $attr_it->get('ReferenceName') == 'Password' )
 		{
			return false; 			
 		}
 		
 		if ( $attr_it->get('ReferenceName') == 'IsShared' )
 		{
			return false; 			
 		}
 		
 		return parent::IsNeedToDisplay( $attr_it );
 	}
 	
	function getIterator( ) 
	{ 
		global $_REQUEST;
		return $this->stats->getActiveUsers( $_REQUEST['days'] ); 
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class ActiveUsersTable extends StatsTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('cms_User');
	}
	
	function getList()
	{
		return new ActiveUsersList( $this->object );
	}
 	
 	function getCaption()
 	{
 		return text('procloud24');
 	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class StatsPage extends Page
 {
 	function getTable() 
 	{
 		global $_REQUEST;
 		
 		switch ( $_REQUEST['mode'] )
 		{
 			default:
 				return new ReportsTable();	
 		}
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
 }

 
 ?>
