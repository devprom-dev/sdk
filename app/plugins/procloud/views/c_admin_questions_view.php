<?php
/*
 * DEVPROM (http://www.devprom.net)
 * questions.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 //////////////////////////////////////////////////////////////////////////////////////////////
 class QuestionsList extends PageList
 {
 	function QuestionsList( $object )
 	{
 		parent::PageList( $object );
 	}
 	
	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
}
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class QuestionsTable extends ViewTable
 {
	function getObject()
	{
 		global $model_factory;
 		return $model_factory->getObject('cms_CheckQuestion');
	}
	
	function getList()
	{
 		$this->object->defaultsort = 'RecordCreated DESC';
		return new QuestionsList( $this->object );
	}

 	function getCaption()
 	{
 		return translate('Список контрольных вопросов');
 	}
 } 

 /////////////////////////////////////////////////////////////////////////////////
 class QuestionsPage extends Page
 {
 	function getTable() {
 		return new QuestionsTable();
 	}

 	function getForm() 
 	{
 		global $model_factory;
 		
 		return new MetaObjectForm( 
 			$model_factory->getObject('cms_CheckQuestion') );
 	}
 }
?>