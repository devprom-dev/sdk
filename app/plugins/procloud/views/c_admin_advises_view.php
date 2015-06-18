<?php
/*
 * DEVPROM (http://www.devprom.net)
 * blacklist.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 //////////////////////////////////////////////////////////////////////////////////////////////
 class AdviseThemeList extends PageList
 {
 	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class AdviseThemeTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('co_AdviseTheme');
	}
	
	function getList()
	{
		return new AdviseThemeList( $this->object );
	}
	
 	function getCaption()
 	{
 		return translate('Тематики советов');
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////////////
 class AdviseList extends PageList
 {
 	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
	
	function getMaxOnPage()
	{
		return 40;
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class AdviseTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('co_Advise');
	}
	
	function getList()
	{
		return new AdviseList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Советы');
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////////////
 class GroupTable extends ViewTable
 {
 	function GroupTable()
 	{
 		$this->table1 = new AdviseThemeTable();
 		$this->table2 = new AdviseTable();
 	}
 	
 	function draw()
 	{
 		echo '<div class="line">';
	 		$this->table1->draw();
	 	echo '</div>';

 		echo '<div class="line">';
	 		$this->table2->draw();
	 	echo '</div>';
 	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class AdvisePage extends Page
 {
 	function getTable() {
 		return new GroupTable();
 	}

 	function getForm() 
 	{
 		global $model_factory, $_REQUEST;
 		
 		if ( $_REQUEST['entity'] == 'co_AdviseTheme' )
 		{
	 		return new MetaObjectForm(
	 			$model_factory->getObject('co_AdviseTheme'));
 		}
		else
		{ 		
	 		return new MetaObjectForm(
	 			$model_factory->getObject('co_Advise'));
		}
 	}
 }
?>