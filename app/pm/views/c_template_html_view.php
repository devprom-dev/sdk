<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class TemplateHTMLForm extends PMPageForm
 {
 	function getCaption() 
 	{
		return translate('Шаблон выгрузки в формате HTML');
	}
	
 	function IsAttributeVisible( $attr_name ) 
 	{
		if($attr_name == 'OrderNum') return false;
		return parent::IsAttributeVisible( $attr_name );
	}
/*
	function getFormPage() {
		return 'artefacts.php';
	}
*/	
 	function IsNeedButtonNew() { return false; }
 	function IsNeedButtonCopy() { return false;	}
 }
  
 //////////////////////////////////////////////////////////////////////////////////////////////
 class TemplateHTMLList extends PageList
 {
	function IsNeedToDisplayLinks( ) { return false; }
	
	function IsNeedToDisplay( $attr ) 
	{
		switch($attr) 
		{
			case 'CSSBlock':
			case 'Header':
			case 'Footer':
				return false;
		}
		return parent::IsNeedToDisplay($attr);
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class TemplateHTMLTable extends PMPageTable
 {
	function getObject()
	{
 		return new TemplateHTML;
	}
	
	function getList()
	{
		return new TemplateHTMLList( $this->object );
	}

	function getCaption() 
	{
		return translate('Шаблоны выгрузки в формате HTML');
	}
 } 

?>