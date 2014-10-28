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
 class RequestSelectWebMethod extends SelectWebMethod
 {
 	function execute_request()
 	{
 		global $_REQUEST;
	 	if($_REQUEST['ChangeRequest'] != '') {
	 		$this->execute($_REQUEST['ChangeRequest'], $_REQUEST['value']);
	 	}
 	}
 	
 	function drawSelect( $request_id, $default_value )
 	{
 		parent::drawSelect( 
			array( 'ChangeRequest' => $request_id ), 
			$default_value );
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class EstimateRequestWebMethod extends RequestSelectWebMethod
 {
     var $scale;
     
     function __construct( $scale = array() )
     {
         $this->scale = $scale;
         
         parent::__construct();
     }
     
 	function hasAccess()
 	{
 		return getFactory()->getAccessPolicy()->can_modify_attribute(getFactory()->getObject('pm_ChangeRequest'), 'Estimation');
 	}
 	
 	function getValues()
 	{
 	    return $this->scale;
 	}
 	
 	function execute ( $request_id, $value )
 	{
 		global $model_factory;
 		$request = $model_factory->getObject('pm_ChangeRequest');
 		
 		$request_it = $request->getExact($request_id);
 		if ( $request_it->count() )
 		{
 			$request->modify_parms($request_it->getId(),
 				array('Estimation' => $value) );
 		}
 	}
 	
 	function getCaption()
 	{
 		return text(889);
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class SetRequestIterationWebMethod extends RequestWebMethod
 {
 	var $request_it;
 	
 	function SetRequestIterationWebMethod( $request_it = null )
 	{
 		$this->request_it = $request_it;
 		
 		parent::RequestWebMethod();
 	}
 	
	function getCaption() 
	{
		return translate('Перенести в итерацию');
	}

	function getLink()
	{
	    global $model_factory;
	    
	    $query = '?mode=bulk&ids='.$this->request_it->getId().'&bulkmode=complete&&operation=AttributeIterations';
	    
	    $item = $model_factory->getObject('Module')->getExact('issues-backlog')->buildMenuItem($query);

		return $item['url'];
	}
 	
 	function hasAccess()
 	{
 		return getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() 
 			&& $this->request_it->get('OpenTasks') != '' && !$this->request_it->IsFinished();
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
 	}
 	
 	function setRequestIt($request_it)
 	{
 		$this->request_it = $request_it;
 	}
 	
	function getCaption() 
	{
		return translate('Перенести в проект');
	}

	function getLink()
	{
		$redirect_url = $_SERVER['REQUEST_URI'];
		
		if ( strpos($redirect_url, "=".$this->request_it->getId()) > 0 )
		{
		    $redirect_url = $this->request_it->object->getPage();
		}
		
		return '?mode=bulk&ids='.$this->request_it->getId().
			'&bulkmode=complete&operation=AttributeProject'.
		    '&redirect='.urlencode($redirect_url);
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
 	}
 	
	function getCaption() 
	{
		return translate('Создать задачу');
	}
	
	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasTasks() 
		    && getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Task'));
	}

	function getJSCall()
	{
		return parent::getJSCall( array(
			'Caption' => $this->request_it->getDisplayName(),
			'ChangeRequest' => $this->request_it->getId(),
			'Priority' => $this->request_it->get('Priority')
		));
	}
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class RequestWikiTraceWebMethod extends RequestWebMethod
 {
 	var $report_it;
 	
 	function RequestWikiTraceWebMethod( $report_ref_name = '' )
 	{
 		global $model_factory;
 		
 		$report = $model_factory->getObject('PMReport');
 		$this->report_it = $report->getExact( $report_ref_name );
 		
 		parent::RequestWebMethod();
 	}
 	
	function getCaption() 
	{
		return $this->report_it->getDisplayName();
	}
	
	function hasAccess()
	{
		return $this->report_it->getId() != '';
	}
	
	function getRedirectUrl()
	{
		return $this->report_it->getUrl();
	}
	
	function getJSCall()
	{
		return parent::getJSCall( 
			array('report' => $this->report_it->getId() )
			);
	}
	
 	function execute_request()
 	{
 		global $_REQUEST, $model_factory;
 		
 		$report = $model_factory->getObject('PMReport');
 		$this->report_it = $report->getExact( $_REQUEST['report'] );
 		
 		if ( $this->report_it->count() > 0 )
 		{
 			echo '&issues='.SanitizeUrl::parseUrl($_REQUEST['objects']);
 		}
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
 	
 	function getJSCall( $class = 'RequestIteratorExportBlog' )
 	{
 		return parent::getJSCall(
 			array( 'class' => $class ) );
 	}
 	
 	function hasAccess()
 	{
 		return getSession()->getProjectIt()->get('IsBlogUsed') == 'Y'
 			&& getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('BlogPost'));
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class BindRequestWebMethod extends RequestWebMethod
 {
 	var $request_it, $trace, $binded_it;
 	
 	function BindRequestWebMethod( $request_it = null, $binded_it = null, $trace = null )
 	{
 		$this->request_it = $request_it;
 		$this->binded_it = $binded_it;
 		$this->trace = $trace;
 		
 		parent::RequestWebMethod();
 	}
 	
	function getUrl()
	{
		return parent::getUrl( 
			array( 'trace' => get_class($this->trace),
				   'binded' => $this->binded_it->getId() ) 
			);
	}
	
	function getRedirectUrl()
	{
		return $this->binded_it->getViewUrl();
	}
	
 	function execute_request()
 	{
 		global $_REQUEST, $model_factory;

		$trace = $model_factory->getObject($_REQUEST['trace']);

		$object = $model_factory->getObject($trace->getObjectClass());
		$object_it = $object->getExact($_REQUEST['binded']);

		$request = $model_factory->getObject('pm_ChangeRequest');
		$request_it = $request->getExact($_REQUEST['target_id']);

		if ( $object_it->count() > 0 && $request_it->count() > 0 )
		{
			$cnt = $trace->getByRefArrayCount(
				array( 'ChangeRequest' => $request_it->getId(),
					   'ObjectId' => $object_it->getId() )
				);
			
			if ( $cnt < 1 )
			{
				$trace->add_parms(
					array('ChangeRequest' => $request_it->getId(),
						  'ObjectId' => $object_it->getId(),
						  'ObjectClass' => $trace->getObjectClass() ) );
			}
					  
			exit(header('Location: '.$object_it->getViewUrl()));
		}
 	}
 } 
 
 ///////////////////////////////////////////////////////////////////////////////////////
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestWebMethod extends FilterWebMethod
 {
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestStateWebMethod extends ViewRequestWebMethod
 {
 	function __construct()
 	{
 		parent::__construct();
 		
 		$this->setDefaultValue(join(',',getFactory()->getObject('Request')->getNonTerminalStates()));
 	}
 	
 	function getCaption()
 	{
 		return translate('Статус');
 	}
 	
 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array ();
 		$values['all'] = translate('Все');
  		
		$state = $model_factory->getObject('IssueState');
 		$issue = $model_factory->getObject('pm_ChangeRequest');
		
		$state_it = $state->getAll();
		while ( !$state_it->end() )
		{
			$values[$state_it->get('ReferenceName')] = $state_it->getDisplayName();
			$state_it->moveNext();
		}

		$state = join(',',$issue->getTerminalStates());
		if ( count($values) > 1 && !array_key_exists($state, $values) ) $values[$state] = translate('Завершено'); 
		
		$state = join(',',$issue->getNonTerminalStates());
		if ( count($values) > 1 && !array_key_exists($state, $values) ) $values[$state] = translate('Не завершено');
		
		return $values;
	}
	
	function getStyle()
	{
		return 'width:185px;';
	}
	
	function getValueParm()
	{
		return 'state';
	}
 }
    
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestSubmittedWebMethod extends FilterAutoCompleteWebMethod
 {
 	function getCaption()
 	{
 		return translate('Обнаружено в версии');
 	}

 	function ViewRequestSubmittedWebMethod()
 	{
 		global $model_factory;
 		
 		parent::FilterAutoCompleteWebMethod(
 			$model_factory->getObject('Version'), $this->getCaption() );
 	}
 	
 	function getValueParm()
 	{
 		return 'subversion';
 	}
 	
 	function getStyle()
 	{
 		return 'width:170px;';
 	}
 	
	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasVersions();
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestTagWebMethod extends ViewRequestWebMethod
 {
 	var $tag_it;
 	
 	function ViewRequestTagWebMethod()
 	{
 		global $model_factory;
 		
 		$tag = $model_factory->getObject('Tag');

 		$request_tag = $model_factory->getObject('pm_RequestTag');
 		$tag->addFilter( new TagRequestFilter('related') );
 		
 		$this->tag_it = $tag->getAll();
 		
 		parent::WebMethod();
 	}
 	
 	function getCaption()
 	{
 		return translate('Тэги');
 	}

 	function getValues()
 	{
  		$values = array (
 			'all' => translate('Все'),
 			);
 		
 		while ( !$this->tag_it->end() )
 		{
 			$values[' '.$this->tag_it->get('TagId')] = $this->tag_it->get('Caption');
 			$this->tag_it->moveNext();
 		}
 		
 		if ( !in_array($this->getValue(), array('', 'all')) )
 		{
	 		$tag_it = $this->tag_it->object->getExact($this->getValue());
 		
     		if ( $tag_it->count() > 0 )
     		{
    			$values[' '.$tag_it->get('TagId')] = $tag_it->get('Caption');
     		}
 		}
 		             		        
		$values[' 0'] = translate('Не заданы');

 		return $values;
	}
	
	function getStyle()
	{
		return 'width:190px;';
	}

 	function getValueParm()
 	{
 		return 'tag';
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestFunctionWebMethod extends FilterAutoCompleteWebMethod
 {
 	function ViewRequestFunctionWebMethod()
 	{
 		global $model_factory;
 		
 		parent::FilterAutoCompleteWebMethod( 
			$model_factory->getObject('Feature'), $this->getCaption() );
 	}

 	function getCaption()
 	{
 		return translate('Функция');
 	}

	function getStyle()
	{
		return 'width:225px;';
	}

 	function getValueParm()
 	{
 		return 'function';
 	}

 	function hasAccess()
 	{
 		return getSession()->getProjectIt()->getMethodologyIt()->HasFeatures();
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewRequestEstimationWebMethod extends ViewRequestWebMethod
 {
 	function getCaption()
 	{
 		return translate('Трудоемкость');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'all' => $this->getCaption().':',
 			'simple' => translate('Простые').' (0..3)',
 			'normal' => translate('Средние').' (4..13)',
 			'hard' => translate('Сложные').' (> 13)',
 			'undefined' => translate('Неоцененные'),
 			);
 		
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
 class ViewRequestVersionWebMethod extends FilterAutoCompleteWebMethod
 {
 	var $object;
 	
 	function ViewRequestVersionWebMethod()
 	{
 		global $model_factory;
 		
 		if ( getSession()->getProjectIt()->getMethodologyIt()->HasVersions() )
 		{
 			$this->object = $model_factory->getObject('Version'); 
 		}
 		else
 		{
 			$this->object = $model_factory->getObject('Stage'); 
 		}
 		
 		parent::FilterAutoCompleteWebMethod( $this->object, $this->getCaption() );
 	}
 	
 	function getCaption()
 	{
 		if ( getSession()->getProjectIt()->getMethodologyIt()->HasVersions() )
 		{
 			return translate('Выполнено в версии');
 		}
 		else
 		{
 			return translate('Выполнено на стадии');
 		}
 	}

 	function getValueParm()
 	{
 		return 'version';
 	}
 	
 	function getStyle()
 	{
 		return 'width:150px;';
 	}
 	
	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasReleases() 
		    || getSession()->getProjectIt()->getMethodologyIt()->HasVersions();
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
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class ModifyRequestWebMethod extends RequestWebMethod
 {
 	var $request_it;
 	
 	function ModifyRequestWebMethod( $request_it = null )
 	{
 		$this->request_it = $request_it;
 		
 		parent::RequestWebMethod();
 	}
 	
	function getCaption() {
		return translate('Изменить атрибут');
	}

 	function execute_request()
 	{
 		if ( is_object($this->request_it) )
 		{
 			$_REQUEST['ChangeRequest'] = join(',', $this->request_it->idsToArray()); 
 		}

	 	if ( $_REQUEST['ChangeRequest'] != '' ) 
	 	{
	 		$this->execute(
	 				$_REQUEST['ChangeRequest'], 
	 				$_REQUEST['attr'], 
	 				IteratorBase::utf8towin($_REQUEST['value']), 
	 				$_REQUEST['notify']
 			);
	 	}
 	}
 	
 	function execute( $object_id, $attribute, $value, $notify )
 	{
 		global $model_factory, $_REQUEST, $project_it;
 		
 		$request = $model_factory->getObject('pm_ChangeRequest');
		
 		$request->removeNotificator( 'EmailNotificator' );

		$request_it = $request->getExact( preg_split('/,/', $object_id) );
		
 		if ( $request_it->count() < 1 ) return;
 		 
		if ( array_key_exists('Tag', $_REQUEST) )
		{
			$request_tag = $model_factory->getObject('pm_RequestTag');
			
 			$request_tag->removeNotificator( 'EmailNotificator' );
			
 			while ( !$request_it->end() )
 			{
    	 		$request_tag_it = $request_tag->getByAK( $request_it->getId(), $value );
     				
    			if ( $request_tag_it->count() < 1 )
    			{
    				$request_tag->add_parms( 
    					array('Request' => $request_it->getId(),
    						  'Tag' => $value ) );
    			}
    			
    			$request_it->moveNext();
 			}
		}
		else if ( array_key_exists('RemoveTag', $_REQUEST) )
		{
			$request_tag = $model_factory->getObject('pm_RequestTag');
			
			while ( !$request_it->end() )
			{
			    $request_tag->removeTags( $request_it->getId() );
			    
			    $request_it->moveNext();
			}
		}
 	}
}
