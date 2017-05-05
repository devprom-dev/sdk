<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ExportWebMethod.php";
include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';
include_once SERVER_ROOT_PATH."pm/classes/project/CloneLogic.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class RequestWebMethod extends WebMethod
 {
 	function RequestWebMethod()
 	{
 		parent::WebMethod();
 	}

 	function execute_request()
 	{
 		global $_REQUEST;
 		
	 	if($_REQUEST['ChangeRequest'] != '') {
	 		$this->execute($_REQUEST['ChangeRequest']);
	 	}
 	}
 }

  ///////////////////////////////////////////////////////////////////////////////////////
 class MoveToProjectWebMethod extends RequestWebMethod
 {
 	var $request_it;
 	
 	function __construct( $request_it = null )
 	{
 		parent::__construct();
 		
 		$this->setRequestIt($request_it);
 		$this->setRedirectUrl(
 				"function(){window.location='".getSession()->getApplicationUrl()."';}"
		);
 	}
 	
 	function setRequestIt($request_it)
 	{
 		$this->request_it = $request_it;
 	}
 	
	function getCaption() 
	{
		return translate('Перенести в проект');
	}

 	function getMethodName()
	{
		return 'AttributeProject';
	}
	
 	function getJSCall( $parms = array() )
	{
 		return "javascript:processBulk('".$this->getCaption()."','?formonly=true&operation=".$this->getMethodName()."&Project=".$parms['Project']."', ".$this->request_it->getId().", ".$this->getRedirectUrl().")";
	}
		
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_modify($this->request_it->object);
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class RequestCreateTaskWebMethod extends ObjectCreateNewWebMethod
 {
 	var $request_it;
 	
 	function __construct( $request_it = null )
 	{
 		parent::__construct( getFactory()->getObject('Task') );

 		$this->setRequestIt($request_it);
 	}
 	
 	function setRequestIt( $request_it )
 	{
 		$this->request_it = $request_it;
		$this->setVpd($this->request_it->get('VPD'));
 	}
 	
	function getCaption() 
	{
		return translate('Задача');
	}
	
	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasTasks() 
		    && getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Task'));
	}

	function getJSCall( $parms = array() )
	{
		return parent::getJSCall(
		    array_merge( $parms,
                array(
			        'ChangeRequest' => $this->request_it->getId()
		        )
            )
        );
	}
 }
  
  ///////////////////////////////////////////////////////////////////////////
 class ReleaseNotesRequestWebMethod extends ExportWebMethod
 {
 	function getCaption()
 	{
 		return translate('Вставить в блог');
 	}
 	
 	function getDiscription()
 	{
 		return text(758);
 	}
 	
 	function url( $class = 'RequestIteratorExportBlog' )
 	{
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
 	
 	function hasAccess()
 	{
 		return getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('BlogPost'));
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestWebMethod extends FilterWebMethod
 {
 }

 ///////////////////////////////////////////////////////////////////////////////////////

    

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestEstimationWebMethod extends ViewRequestWebMethod
 {
	 private $scale = array();

	 function __construct( $scale = array() ) {
		 $this->scale = $scale;
		 parent::__construct();
	 }

	 function getCaption() {
 		return translate('Трудоемкость');
 	 }

 	 function getValues()
 	 {
		$values = array (
 			'all' => translate('Все'),
		);
		$values = array_merge($values, $this->scale);
		$values['none'] = translate('Неоцененные');
		return $values;
	 }
	
	 function getStyle()
	 {
		return 'width:125px;';
	 }

 	 function getValueParm()
 	 {
 		return 'estimation';
 	 }

 	 function getType()
 	 {
 		return 'singlevalue';
 	 }
 }

 //////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestListViewWebMethod extends ViewRequestWebMethod
 {
 	var $default_value = 'list';
 	
 	function setDefaultValue( $value )
 	{
 		$this->default_value = $value;
 	}
 	
 	function getCaption()
 	{
 		return translate('Вид');
 	}

 	function getValues()
 	{
  		return array (
 			'list' => translate('Список'), 
 			'board' => translate('Доска'),
 			'trace' => translate('Трассировка'),
 			'chart' => translate('График')
 			);
	}

	function getStyle()
	{
		return 'width:110px;';
	}

 	function getValueParm()
 	{
 		return 'view';
 	}
 
 	function getValue()
 	{
 		$value = parent::getValue();
 		
 		if ( $value == '' )
 		{
 			return $this->default_value; 
 		}
 		
 		return $value;
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 	
 }

  ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestTaskTypeWebMethod extends ViewRequestWebMethod
 {
 	function getCaption()
 	{
 		return text(1107);
 	}
 	
 	function getStyle()
 	{
 		return 'width:130px;';
 	}
 	
 	function getValues()
 	{
 		global $model_factory, $_REQUEST;
 		
 		$type = $model_factory->getObject('pm_TaskType');
 		$type->addSort ( new SortAttributeClause('Caption') );
 		$type_it = $type->getAll();
 		
 		$values = array( 'all' => translate('Все') );
 		
 		while ( !$type_it->end() )
 		{
 			$values[$type_it->get('ReferenceName')] = $type_it->getDisplayName();
 			$type_it->moveNext();
 		}

 		return $values;
 	}

	function getValueParm()
	{
		return 'tasktype';
	}
	
	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasTasks();
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestTaskStateWebMethod extends ViewRequestWebMethod
 {
 	function getCaption()
 	{
 		return text(1108);
 	}
 	
	function getValueParm()
	{
		return 'taskstate';
	}

 	function getValues()
 	{
 		global $model_factory;
 		
 		$values = array( 
			'all' => translate('Все'),
		);
 		
		$state = $model_factory->getObject('TaskState');
		
		$state_it = $state->getAll();
		while ( !$state_it->end() )
		{
			if ( $state_it->get('ReferenceName') == 'resolved' )
			{
				$values['notresolved'] = translate('Не выполнено');
			}
			$values[$state_it->get('ReferenceName')] = $state_it->getDisplayName();
			$state_it->moveNext();
		}

 		return $values;
 	}

 	function getStyle()
 	{
 		return 'width:190px;';
 	}

	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasTasks();
	}
 }
