<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class PMWikiFilterWebMethod extends FilterWebMethod
 {
 	function PMWikiFilterWebMethod()
 	{
 		parent::FilterWebMethod();
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewWikiModifiedAfterDateWebMethod extends FilterDateWebMethod
 {
 	function getCaption()
 	{
 		return translate('Изменено после');
 	}

	function getStyle()
	{
		return 'width:100px;';
	}

	function getValueParm()
	{
		return 'modifiedafter';
	}
 }

 //////////////////////////////////////////////////////////////////////////////////////
 class WikiFilterViewWebMethod extends PMWikiFilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Вид');
 	}
 	
 	function getValues()
 	{
  		return array (
 			'list' => translate('Список'), 
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
 			return 'list'; 
 		}
 		
 		return $value;
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////////
 class WikiFilterActualLinkWebMethod extends PMWikiFilterWebMethod
 {
 	function getCaption()
 	{
 		return text(1043);
 	}
 	
 	function getValues()
 	{
  		return array (
 			'all' => text(2248),
 			'actual' => text(2249),
 			'nonactual' => text(2250),
            'empty' => text(2251)
 			);
	}

	function getStyle()
	{
		return 'width:110px;';
	}

 	function getValueParm()
 	{
 		return 'linkstate';
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 } 
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class SearchWikiToTraceWebMethod extends PMWikiFilterWebMethod
 {
 	var $trace, $binded_it, $target;
 	
 	function SearchWikiToTraceWebMethod( $binded_it = null, $trace = null, $target = null )
 	{
 		$this->binded_it = $binded_it;
 		$this->trace = $trace;
 		$this->target = $target;
 		
 		parent::PMWikiFilterWebMethod();
 	}
 	
	function getCaption() 
	{
		return text(765);
	}
	
	function getTrace()
	{
		return $this->trace;
	}
	
	function getBindedIt()
	{
		return $this->binded_it;
	}
	
	function getJSCall( $parms_array = array() )
	{
		return parent::getJSCall( 
			array( 'binded' => $this->binded_it->getId(),
				   'trace' => get_class($this->trace),
				   'target' => get_class($this->target),
				   'direction' => $parms_array['direction'] )
			);
	}
	
	function getRedirectUrl()
	{
		return 'search.php?select&kind='.get_class($this->target);
	}
	
	function execute_request()
	{
		global $_REQUEST, $model_factory;
		
		$trace = $model_factory->getObject($_REQUEST['trace']);

		$object = $model_factory->getObject('WikiPage');
		$object_it = $object->getExact($_REQUEST['binded']);
		
		$method = new CreateWikiLinkWebMethod($object_it, $trace);
		$method->setDirection( $_REQUEST['direction'] );
		
		echo '&redirect='.urlencode($method->getUrl());
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class CreateWikiLinkWebMethod extends SearchWikiToTraceWebMethod
 {
 	var $direction;
 	
 	function setDirection( $direction )
 	{
 		$this->direction = $direction;
 	}
 	
	function getUrl()
	{
		$binded_it = $this->getBindedIt();
		$trace = $this->getTrace();
		
		return parent::getUrl( 
			array( 'trace' => get_class($trace),
				   'binded' => $binded_it->getId(),
				   'direction' => $this->direction ) 
			);
	}
	
	function getRedirectUrl()
	{
		$binded_it = $this->getBindedIt();
		return $this->binded_it->getViewUrl();
	}
	
	function execute_request()
 	{
 		global $model_factory, $_REQUEST;
 		
 		$page = $model_factory->getObject('WikiPage');
 		$page_it = $page->getExact($_REQUEST['binded']);
 		
 		$link = $model_factory->getObject($_REQUEST['trace']);
 		
 		if ( $_REQUEST['direction'] == 'backward' )
 		{
	 		$link_it = $link->getByRefArray(
	 			array( 'TargetPage' => $page_it->getId(), 
	 				   'SourcePage' => $_REQUEST['target_id'] ) );
	 			
	 		if( $link_it->count() < 1 )
	 		{
		 		$link->add_parms(
		 			array( 'SourcePage' => $_REQUEST['target_id'],
		 				   'TargetPage' => $page_it->getId(),
		 				   'IsActual' => 'Y' ) );
	 		}
 		}
 		else
 		{
	 		$link_it = $link->getByRefArray(
	 			array( 'SourcePage' => $page_it->getId(), 
	 				   'TargetPage' => $_REQUEST['target_id'] ) );
	 			
	 		if( $link_it->count() < 1 )
	 		{
		 		$link->add_parms(
		 			array( 'TargetPage' => $_REQUEST['target_id'],
		 				   'SourcePage' => $page_it->getId(),
		 				   'IsActual' => 'Y' ) );
	 		}
 		}

		exit(header('Location: '.$page_it->getViewUrl()));
 	 }
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class DeleteWikiLinkWebMethod extends PMWikiFilterWebMethod
 {
	function getCaption() 
	{
		return translate('Удалить');
	}
	
	function getDescription() 
	{
		return text(767);
	}
	
	function getUrl( $link_it ) 
	{
		return parent::getUrl(
			array( 'taget' => $link_it->get('TargetPage'),
				   'source' => $link_it->get('SourcePage') ) );
	}
	
 	function execute_request()
 	{
 		global $_REQUEST;
	 	if($_REQUEST['taget'] != '' && $_REQUEST['source'] != '') {
	 		$this->execute($_REQUEST['taget'], $_REQUEST['source']);
	 	}
 	}
 	
 	function execute( $target, $source )
 	{
 		global $model_factory;
 		
 		$link = $model_factory->getObject('WikiPageTrace');
 		
 		$link_it = $link->getByRefArray(
 			array( 'TargetPage' => $target, 'SourcePage' => $source ) );
 			
 		if( $link_it->count() > 0 )
 		{
	 		$link->delete( $link_it->getId() );
 		}
 	}
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class RevertWikiWebMethod extends PMWikiFilterWebMethod
 {
	function getCaption() 
	{
		return translate('Отменить');
	}
	
	function url( $object_it, $change_it )
	{
		return parent::getJSCall( array( 
				'wiki' => $object_it->getId(),
				'class' => get_class($object_it->object),
				'logid' => $change_it->getId() 
		));
	}
	
 	function execute_request()
 	{
 		$class = getFactory()->getClass($_REQUEST['class']);
 		if ( !class_exists($class) ) return;
 		
 		$object = getFactory()->getObject($class);
 		$object_it = $object->getExact( $_REQUEST['wiki'] );
 		
 		if ( getFactory()->getAccessPolicy()->can_modify($object_it) )
 		{
 			$object_it->Revert();
 			
 			$log_it = getFactory()->getObject('ChangeLog')->getExact($_REQUEST['logid']);
 			if ( $log_it->getId() != '' ) $log_it->object->delete($log_it->getId()); 
 		}
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewWikiTagWebMethod extends PMWikiFilterWebMethod
 {
 	var $tag_it;
 	
 	function ViewWikiTagWebMethod( $object = null )
 	{
 		global $model_factory;
 		
 		$tag = $model_factory->getObject('WikiTag');

 		if ( is_object($object) )
 		{
	 		$tag->addFilter( new WikiTagReferenceFilter($object->getReferenceName()) );
 		}

	 	$this->tag_it = $tag->getAll();
 		
 		parent::PMWikiFilterWebMethod();
 	}
 	
 	function getCaption()
 	{
 		return translate('Тэги');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'all' => translate('Все'),
			' 0' => translate('<нет значения>')
		);
		$items = array();

 		while ( !$this->tag_it->end() )
 		{
 			$items[$this->tag_it->get('Caption')][] = $this->tag_it->get('Tag');
 			$this->tag_it->moveNext();
 		}
		foreach( $items as $key => $ids ) {
			$items[$key] = ' '.join('-',$ids);
		}
		$values = array_merge($values, array_flip($items));

 		if ( !in_array($this->getValue(), array('', 'all')) )
 		{
     		$tag = $model_factory->getObject('Tag');
     		
     		$tag_it = $tag->getExact($this->getValue());
     		
     		if ( $tag_it->count() > 0 )
     		{
    			$values[' '.$tag_it->get('TagId')] = $tag_it->get('Caption');
     		}
 		}
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:120px;';
	}

 	function getValueParm()
 	{
 		return 'tag';
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewWikiArchivedWebMethod extends PMWikiFilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('В архиве');
 	}

 	function getValues()
 	{
  		$values = array (
 			'current' => translate('Нет'),
  			'archived' => translate('В архиве')
 			);
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:130px;';
	}

 	function getValueParm()
 	{
 		return 'archive';
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewWikiCoverageWebMethod extends PMWikiFilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Покрытие');
 	}

 	function getValues()
 	{
  		$values = array (
 			'all' => translate('Любое'),
  			'none' => translate('Без требований')
 			);
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:130px;';
	}

 	function getValueParm()
 	{
 		return 'coverage';
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 class WikiFilterHistoryFormattingWebMethod extends PMWikiFilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Форматирование');
 	}
 	
 	function getValues()
 	{
  		return array (
 			'text' => translate('Только текст'),
  			'full' => translate('Текст и стили') 
 			);
	}

	function getStyle()
	{
		return 'width:110px;';
	}

 	function getValueParm()
 	{
 		return 'formatting';
 	}
 
 	function getValue()
 	{
 		$value = parent::getValue();
 		
 		if ( $value == '' )
 		{
 			return 'text'; 
 		}
 		
 		return $value;
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 } 
