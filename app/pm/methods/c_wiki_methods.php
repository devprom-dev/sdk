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
 class ViewWikiCoverageWebMethod extends PMWikiFilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Трассировка');
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
