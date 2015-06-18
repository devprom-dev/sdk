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
 class ServiceCategoryList extends PageList
 {
 	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class ServiceCategoryTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('co_ServiceCategory');
	}
	
	function getList()
	{
		return new ServiceCategoryList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Категории услуг');
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////////////
 class ServiceList extends PageList
 {
 	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
 }

 //////////////////////////////////////////////////////////////////////////////////////////////
 class ServiceTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('co_Service');
	}
	
	function getList()
	{
		return new ServiceList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Услуги');
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////////////
 class GroupTable extends ViewTable
 {
 	function GroupTable()
 	{
 		$this->table1 = new ServiceCategoryTable();
 		$this->table2 = new ServiceTable();
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
 class ServicePage extends Page
 {
 	function getTable()
 	{
 		return new GroupTable;
 	}
 	
 	function getForm() 
 	{
 		global $model_factory, $_REQUEST;
 		
 		if ( $_REQUEST['entity'] == 'co_ServiceCategory' )
 		{
	 		return new MetaObjectForm(
	 			$model_factory->getObject('co_ServiceCategory'));
 		}
 		else
 		{
	 		return new MetaObjectForm(
	 			$model_factory->getObject('co_Service'));
 		}
 	}
 }

?>