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
 class BillsList extends PageList
 {
 	function BillsList( $object )
 	{
 		parent::PageList( $object );
 	}
 	
	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
	
	function drawRefCell( $entity_it, $object_it, $attr )
	{
		switch ( $entity_it->object->getClassName() )
		{
			case 'co_Bill':
				$bill_it = $object_it->getRef('Bill');
				echo '<a href="/co/account.php?user='.$bill_it->get('SystemUser').'">'.
					$bill_it->getDisplayName().'</a>';
				
				$user_it = $bill_it->getRef('SystemUser');
				echo ' ('.$bill_it->getBalance().' руб., '.$user_it->getRefLink().')';
			
				break;

			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class BillsTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('co_BillOperation');
	}
	
	function getList()
	{
		return new BillsList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Операции по счетам пользователей');
 	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class BillsPage extends Page
 {
 	function getTable() {
 		return new BillsTable();
 	}

 	function getForm() 
 	{
 		global $model_factory;
 		
 		return new MetaObjectForm( 
 			$model_factory->getObject('co_BillOperation') );
 	}
 }
?>