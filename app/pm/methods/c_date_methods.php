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
class ViewStartDateWebMethod extends FilterDateWebMethod
{
 	function __construct ( $title = 'Начало' )
 	{
		parent::__construct();
        $this->setCaption(translate($title));
        $this->setDefault(getSession()->getLanguage()->getPhpDate(strtotime('-1 weeks', strtotime(date('Y-m-j')))));
 	}

	function getStyle()
	{
		return 'width:100px;';
	}

	function getValueParm()
	{
		return 'start';
	}

	function getPersistedValue2()
	{
		return null;
	}

	function getValue2()
	{
		$value = SystemDateTime::parseRelativeDateTime($_REQUEST[$this->getValueParm()], getLanguage());
		if ( in_array($value, array('','hide')) ) return $this->getDefault();
		return $value;
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class ViewFinishDateWebMethod extends FilterDateWebMethod
{
	 function __construct( $title = 'Окончание' )
	 {
		 parent::__construct();
         $this->setCaption(translate($title));
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
	 function __construct() {
		 parent::__construct();
		 $this->setCaption(translate('Изменено после'));
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
