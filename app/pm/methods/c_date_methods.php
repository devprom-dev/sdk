<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewDateWebMethod extends FilterWebMethod
 {
	function getType()
	{
		return 'singlevalue';
	}
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewDateYearWebMethod extends ViewDateWebMethod
 {
 	function getCaption()
 	{
 		return translate('Год');
 	}
 	
 	function getValues()
 	{
 		global $model_factory;
 		
 		$values = array();
 		
 		$year = $model_factory->getObject('DateYear');
 		$year_it = $year->getAll();

 		for ( $i = 0; $i < $year_it->count(); $i++ )
 		{
 			$values[$year_it->getDisplayName()] = $year_it->getDisplayName();
 			$year_it->moveNext();
 		}

  		return $values;
	}
	
	function getStyle()
	{
		return 'width:40px;';
	}
	
	function getValueParm()
	{
		return 'year';
	}
	
	function getValue()
	{
		$value = parent::getValue();
		
		if ( in_array($value, array('all','')) )
		{
			return date('Y');
		}
		
		return $value;
	}

	function getFreezeMethod()
	{
		return null;
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewDateMonthWebMethod extends ViewDateWebMethod
 {
 	function getCaption()
 	{
 		return translate('Месяц');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
 		$values = array();
 		
 		$month = $model_factory->getObject('DateMonth');
 		$month_it = $month->getAll();
 		
 		while ( !$month_it->end() )
 		{
 			$values[$month_it->getId()] = $month_it->getDisplayName();
 			$month_it->moveNext();
 		}
 		
  		return $values;
	}
	
	function getStyle()
	{
		return 'width:60px;';
	}
	
	function getValueParm()
	{
		return 'month';
	}

	function getValue()
	{
		$value = parent::getValue();
		
		if ( in_array($value, array('all','')) )
		{
			return date('m');
		}
		
		return $value;
	}

	function getFreezeMethod()
	{
		return null;
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
class ViewStartDateWebMethod extends FilterDateWebMethod
{
 	function __construct ( $title = 'Начало' )
 	{
 		$this->setCaption(translate($title));
 		$this->setDefault(getSession()->getLanguage()->getPhpDate(strtotime('-1 weeks', strtotime(date('Y-m-j')))));
		parent::__construct();
 	}

	function getStyle()
	{
		return 'width:100px;';
	}

	function getValueParm()
	{
		return 'start';
	}

	function getPersistedValue()
	{
		return null;
	}

	function getValue()
	{
		$value = $_REQUEST[$this->getValueParm()];
		if ( in_array($value, array('','all')) ) return $this->getDefault();
		return $value;
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class ViewFinishDateWebMethod extends FilterDateWebMethod
{
	 function __construct( $title = 'Окончание' )
	 {
		 $this->setCaption(translate($title));
		 parent::__construct();
	 }

	function getStyle() {
		return 'width:100px;';
	}

	function getValueParm() {
		return 'finish';
	}
}

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewModifiedBeforeDateWebMethod extends FilterDateWebMethod
 {
 	function getCaption()
 	{
 		return translate('Изменено до');
 	}

	function getStyle()
	{
		return 'width:100px;';
	}

	function getValueParm()
	{
		return 'modifiedbefore';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewModifiedAfterDateWebMethod extends FilterDateWebMethod
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
