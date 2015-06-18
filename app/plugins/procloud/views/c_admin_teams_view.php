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
 class TeamList extends PageList
 {
 	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class TeamTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('co_Team');
	}
	
	function getList()
	{
		return new TeamList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Команды');
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////////////
 class GroupTable extends ViewTable
 {
 	function GroupTable()
 	{
 		$this->table1 = new TeamTable();
 		//$this->table2 = new AdviseTable();
 	}
 	
 	function draw()
 	{
 		echo '<div class="line">';
	 		$this->table1->draw();
	 	echo '</div>';

 		echo '<div class="line">';
	 		//$this->table2->draw();
	 	echo '</div>';
 	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class TeamPage extends Page
 {
 	function getTable() {
 		return new GroupTable();
 	}

 	function getForm() 
 	{
 		global $model_factory, $_REQUEST;
 		
 		if ( $_REQUEST['entity'] == 'co_Team' )
 		{
	 		return new MetaObjectForm(
	 			$model_factory->getObject('co_Team'));
 		}
 	}
 }
?>