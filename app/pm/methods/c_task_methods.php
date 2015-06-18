<?php
 
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

///////////////////////////////////////////////////////////////////////////////////////
 class TaskWebMethod extends WebMethod
 {
 	function TaskWebMethod()
 	{
 		parent::WebMethod();
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class MoveTaskWebMethod extends TaskWebMethod
 {
 	var $release_it;
 	
 	function MoveTaskWebMethod( $release_it = null )
 	{
 		$this->release_it = $release_it;
 		
 		parent::TaskWebMethod();
 	}
 	
	function getCaption() 
	{
		return translate('Перенести в итерацию').' '.
			$this->release_it->getDisplayName();
	}

 	function execute_request()
 	{
 		global $_REQUEST;
	 	if($_REQUEST['Task'] != '' && $_REQUEST['Release'] != '') {
	 		$this->execute($_REQUEST['Task'], $_REQUEST['Release']);
	 	}
 	}
 	
 	function execute( $task_id, $release_id )
 	{
 		$task = getFactory()->getObject('pm_Task');
 		$task_it = $task->getExact($task_id);
 		
 		if ( getFactory()->getAccessPolicy()->can_modify($task_it) )
 		{
 			$task->modify_parms($task_it->getId(), array( 'Release' => $release_id ));
 		}
 	}
 }
 
 
 //////////////////////////////////////////////////////////////////////////////////////
 class ViewTaskWebMethod extends FilterWebMethod
 {
 	var $ids;

 	function ViewTaskWebMethod ( $parms_array = array() )
 	{
 		$this->ids = $parms_array;
 		
 		parent::FilterWebMethod();
 	}

 	function execute ( $setting, $value )
 	{
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewTaskDateWebMethod extends FilterDateWebMethod
 {
 	function getStyle()
 	{
 		return 'width:70px;height:18px;';
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewTaskListWebMethod extends ViewTaskWebMethod
 {
 	function getCaption()
 	{
 		return translate('Вид');
 	}
 	
 	function hasAccess()
 	{
 		return true;
 	}
 	
 	function getStyle()
 	{
 		return 'width:110px;';
 	}
 	
 	function getValues()
 	{
 		$values = array( 
			'board' => translate('Доска задач'), 
			'tasks' => translate('Список задач'), 
			'issues' => translate('Пожелания'), 
			'trace' => translate('Трассировка'), 
			'chart' => translate('График') 
		);
 		
 		return $values;
 	}

	function getValueParm()
	{
		return 'view';
	}
	
	function getValue()
	{
		$value = parent::getValue();
		
		if ( $value != '' )
		{
			return $value;
		}
		else
		{
			return 'tasks';
		}
	}
	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewTaskListShortWebMethod extends ViewTaskListWebMethod
 {
 	function getValues()
 	{
 		$values = array( 
			'list' => translate('Список задач'), 
			'board' => translate('Доска задач'), 
			'trace' => translate('Трассировка'),
			'chart' => translate('График') 
		);
 		
 		return $values;
 	}
 }

